<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestNoteController extends Controller
{
    public function store(Request $request, ServiceRequest $serviceRequest)
    {
        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $serviceRequest->notes()->create([
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Note added.');
    }
}
