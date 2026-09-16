<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\User;
use App\Notifications\ServiceRequestUpdated;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function board()
    {
        $columns = [
            ServiceRequest::STATUS_PENDING,
            ServiceRequest::STATUS_ASSIGNED,
            ServiceRequest::STATUS_IN_PROGRESS,
            ServiceRequest::STATUS_COMPLETED,
        ];

        $serviceRequests = ServiceRequest::whereIn('status', $columns)
            ->with(['customer', 'technician', 'category'])
            ->latest()
            ->get()
            ->groupBy('status');

        return view('service-requests.board', compact('columns', 'serviceRequests'));
    }

    public function index(Request $request)
    {
        $query = ServiceRequest::query()->with(['customer', 'technician', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('technician_id')) {
            $query->where('assigned_technician_id', $request->technician_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $serviceRequests = $query->latest()->paginate(15)->withQueryString();

        $technicians = Technician::orderBy('name')->get();

        return view('service-requests.index', compact('serviceRequests', 'technicians'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $categories = ServiceCategory::orderBy('name')->get();
        $technicians = Technician::where('is_active', true)->orderBy('name')->get();

        return view('service-requests.create', compact('customers', 'categories', 'technicians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'required_without:new_customer_name', 'exists:customers,id'],
            'new_customer_name' => ['nullable', 'required_without:customer_id', 'string', 'max:255'],
            'new_customer_phone' => ['nullable', 'string', 'max:50', 'unique:customers,phone'],
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'assigned_technician_id' => ['nullable', 'exists:technicians,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'service_address' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'in:'.implode(',', ServiceRequest::PRIORITIES)],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        if (empty($data['customer_id']) && ! empty($data['new_customer_name'])) {
            $customer = Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'] ?? null,
            ]);
            $data['customer_id'] = $customer->id;
        }
        unset($data['new_customer_name'], $data['new_customer_phone']);

        $data['created_by'] = $request->user()->id;
        $data['status'] = $data['assigned_technician_id'] ?? null
            ? ServiceRequest::STATUS_ASSIGNED
            : ServiceRequest::STATUS_PENDING;

        $serviceRequest = ServiceRequest::create($data);
        $this->notifyAdmins($serviceRequest, "New booking {$serviceRequest->ticket_number} has been confirmed.");
        $this->notifyClient($serviceRequest, "Your booking {$serviceRequest->ticket_number} has been confirmed.");

        return redirect()->route('service-requests.show', $serviceRequest)->with('status', 'Ticket created.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $serviceRequest->load(['customer', 'technician', 'category', 'creator', 'notes.user', 'attachments.uploader', 'invoice.items']);

        $technicians = Technician::where('is_active', true)->orderBy('name')->get();

        return view('service-requests.show', compact('serviceRequest', 'technicians'));
    }

    public function edit(ServiceRequest $serviceRequest)
    {
        $customers = Customer::orderBy('name')->get();
        $categories = ServiceCategory::orderBy('name')->get();
        $technicians = Technician::where('is_active', true)->orderBy('name')->get();

        return view('service-requests.edit', compact('serviceRequest', 'customers', 'categories', 'technicians'));
    }

    public function update(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'service_address' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', 'in:'.implode(',', ServiceRequest::PRIORITIES)],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $serviceRequest->update($data);

        return redirect()->route('service-requests.show', $serviceRequest)->with('status', 'Ticket updated.');
    }

    public function destroy(ServiceRequest $serviceRequest)
    {
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')->with('status', 'Ticket deleted.');
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', ServiceRequest::STATUSES)],
        ]);

        $previousStatus = $serviceRequest->status;
        $serviceRequest->status = $data['status'];
        if ($data['status'] === ServiceRequest::STATUS_COMPLETED) {
            $serviceRequest->completed_at = now();
        }
        $serviceRequest->save();
        if ($previousStatus !== $serviceRequest->status) {
            $this->notifyAdmins($serviceRequest, "{$serviceRequest->ticket_number} is now " . $serviceRequest->statusLabel() . '.');
            $this->notifyClient($serviceRequest, "Your service request {$serviceRequest->ticket_number} is now " . $serviceRequest->statusLabel() . '.');
        }

        return back()->with('status', 'Status updated to '.$serviceRequest->statusLabel().'.');
    }

    public function assign(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'assigned_technician_id' => ['nullable', 'exists:technicians,id'],
        ]);

        $previousTechnician = $serviceRequest->assigned_technician_id;
        $serviceRequest->assigned_technician_id = $data['assigned_technician_id'];
        if ($data['assigned_technician_id'] && $serviceRequest->status === ServiceRequest::STATUS_PENDING) {
            $serviceRequest->status = ServiceRequest::STATUS_ASSIGNED;
        }
        $serviceRequest->save();
        if ($serviceRequest->assigned_technician_id && $previousTechnician !== $serviceRequest->assigned_technician_id) {
            $this->notifyAdmins($serviceRequest, "{$serviceRequest->ticket_number} was assigned to {$serviceRequest->technician->name}.");
            $this->notifyClient($serviceRequest, "A technician has been assigned to your service request {$serviceRequest->ticket_number}.");
        }

        return back()->with('status', 'Technician assignment updated.');
    }

    private function notifyAdmins(ServiceRequest $serviceRequest, string $message): void
    {
        foreach (User::all() as $admin) {
            $admin->notify(new ServiceRequestUpdated($serviceRequest, $message));
        }
    }

    private function notifyClient(ServiceRequest $serviceRequest, string $message): void
    {
        if ($serviceRequest->customer && $serviceRequest->customer->email) {
            $serviceRequest->customer->notify(new ServiceRequestUpdated($serviceRequest, $message));
        }
    }
}
