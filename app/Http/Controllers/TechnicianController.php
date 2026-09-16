<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TechnicianController extends Controller
{
    public function index()
    {
        $users = Technician::with('serviceCategories')->orderBy('name')->paginate(20);
        return view('technicians.index', compact('users'));
    }

    public function create()
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('technicians.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:technicians,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:technicians,phone'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'service_categories' => ['nullable', 'array'],
            'service_categories.*' => ['exists:service_categories,id'],
        ]);

        $technician = Technician::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => ! empty($data['password']) ? Hash::make($data['password']) : null,
        ]);
        $technician->serviceCategories()->sync($data['service_categories'] ?? []);

        return redirect()->route('technicians.index')->with('status', 'Technician added.');
    }

    public function edit(Technician $technician)
    {
        $categories = ServiceCategory::orderBy('name')->get();
        return view('technicians.edit', compact('technician', 'categories'));
    }

    public function update(Request $request, Technician $technician)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:technicians,email,' . $technician->id],
            'phone' => ['required', 'string', 'max:30', 'unique:technicians,phone,' . $technician->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'service_categories' => ['nullable', 'array'],
            'service_categories.*' => ['exists:service_categories,id'],
        ]);

        $technician->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => ! empty($data['password']) ? Hash::make($data['password']) : $technician->password,
        ]);
        $technician->serviceCategories()->sync($data['service_categories'] ?? []);

        return redirect()->route('technicians.index')->with('status', 'Technician updated.');
    }

    public function toggleStatus(Technician $technician)
    {
        $technician->update(['is_active' => ! $technician->is_active]);
        return back()->with('status', 'Technician status updated.');
    }

    public function completed()
    {
        $technicians = Technician::orderBy('name')->get();

        $technicians->each(function ($t) {
            $completed = $t->assignedServiceRequests()->where('status', ServiceRequest::STATUS_COMPLETED)
                ->with('customer')
                ->latest()
                ->take(10)
                ->get();
            $t->completed_count = $completed->count();
            $t->recent_completed = $completed;
        });

        $technicians = $technicians->sortByDesc('completed_count');

        return view('technicians.complete', compact('technicians'));
    }

    public function assign()
    {
        $technicians = Technician::where('is_active', true)->orderBy('name')->get();
        $requests = ServiceRequest::whereNotIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED])->orderBy('scheduled_at')->get();
        return view('technicians.assign', compact('technicians', 'requests'));
    }

    public function storeAssignment(Request $request)
    {
        $data = $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
            'request_id' => ['required', 'exists:service_requests,id'],
        ]);

        $sr = ServiceRequest::findOrFail($data['request_id']);
        $tech = Technician::findOrFail($data['technician_id']);

        $sr->assigned_technician_id = $tech->id;
        $sr->status = ServiceRequest::STATUS_ASSIGNED;
        $sr->save();

        $sr->notes()->create([
            'user_id' => $request->user()->id,
            'body' => 'Assigned to technician: ' . $tech->name,
        ]);

        return redirect()->route('technicians.assign')->with('status', 'Technician assigned.');
    }
}
