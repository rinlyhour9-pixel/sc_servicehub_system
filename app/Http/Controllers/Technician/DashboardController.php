<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $technician = $request->user('technician');

        $jobs = ServiceRequest::where('assigned_technician_id', $technician->id)
            ->with(['customer', 'category', 'notes'])
            ->latest('created_at')
            ->get();

        $openStatuses = [ServiceRequest::STATUS_PENDING, ServiceRequest::STATUS_ASSIGNED, ServiceRequest::STATUS_IN_PROGRESS];

        $openJobs = $jobs->whereIn('status', $openStatuses)->values();
        $completedJobs = $jobs->where('status', ServiceRequest::STATUS_COMPLETED)->values();

        return view('technician.dashboard', compact('technician', 'openJobs', 'completedJobs'));
    }

    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->assigned_technician_id === $request->user('technician')->id, 403);

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', ServiceRequest::STATUSES)],
        ]);

        $serviceRequest->status = $data['status'];
        if ($data['status'] === ServiceRequest::STATUS_COMPLETED) {
            $serviceRequest->completed_at = now();
        }
        $serviceRequest->save();

        return back()->with('status', 'Status updated to '.$serviceRequest->statusLabel().'.');
    }

    public function storeNote(Request $request, ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->assigned_technician_id === $request->user('technician')->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $serviceRequest->notes()->create([
            'technician_id' => $request->user('technician')->id,
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Note added.');
    }
}
