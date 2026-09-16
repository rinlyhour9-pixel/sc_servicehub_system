<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use Illuminate\Http\Request;

class ServiceRequestAttachmentController extends Controller
{
    public function store(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = $request->file('file')->store('attachments/'.$serviceRequest->id, 'public');

        $serviceRequest->attachments()->create([
            'uploaded_by' => $request->user()->id,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('status', 'File uploaded.');
    }

    public function destroy(ServiceRequestAttachment $attachment)
    {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        $serviceRequest = $attachment->serviceRequest;
        $attachment->delete();

        return redirect()->route('service-requests.show', $serviceRequest)->with('status', 'Attachment removed.');
    }
}
