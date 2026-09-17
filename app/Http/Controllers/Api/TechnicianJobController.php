<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingMedia;
use App\Models\JobReport;
use App\Services\BookingNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TechnicianJobController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['status' => ['nullable', 'in:assigned,in_progress,completed']]);
        $query = Booking::with(['service', 'client'])->where('technician_id', $request->user()->id)->latest('scheduled_at');
        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return BookingResource::collection($query->paginate(20));
    }

    public function start(Request $request, Booking $booking)
    {
        abort_unless($booking->technician_id === $request->user()->id, 403);
        if ($booking->status !== Booking::STATUS_ASSIGNED) {
            abort(422, 'Only assigned jobs can be started.');
        }
        $booking->update(['status' => Booking::STATUS_IN_PROGRESS, 'started_at' => now()]);
        BookingNotifier::send($booking->client, 'Technician en route', 'Your technician has started the job.', $booking);

        return new BookingResource($booking->fresh()->load(['service', 'client']));
    }

    public function complete(Request $request, Booking $booking)
    {
        abort_unless($booking->technician_id === $request->user()->id, 403);
        if ($booking->status !== Booking::STATUS_IN_PROGRESS) {
            abort(422, 'Only in-progress jobs can be completed.');
        }
        $data = $request->validate([
            'report' => ['required', 'string', 'max:10000'],
            'metadata' => ['nullable', 'array'],
            'photos' => ['nullable', 'array', 'max:12'],
            'photos.*' => ['image', 'max:5120'],
        ]);
        DB::transaction(function () use ($request, $booking, $data) {
            JobReport::create(['booking_id' => $booking->id, 'technician_id' => $request->user()->id, 'report' => $data['report'], 'metadata' => $data['metadata'] ?? null]);
            foreach ($request->file('photos', []) as $photo) {
                $path = $photo->store("bookings/{$booking->id}/completion", 'public');
                BookingMedia::create(['booking_id' => $booking->id, 'uploaded_by' => $request->user()->id, 'type' => 'completion', 'path' => $path, 'original_name' => $photo->getClientOriginalName(), 'mime_type' => $photo->getMimeType()]);
            }
            $booking->update(['status' => Booking::STATUS_COMPLETED, 'completed_at' => now()]);
        });
        BookingNotifier::send($booking->client, 'Job completed', 'Your technician has submitted the completion report.', $booking->fresh());

        return new BookingResource($booking->fresh()->load(['service', 'client', 'media', 'report']));
    }

    public function availability(Request $request)
    {
        $data = $request->validate(['availability' => ['required', 'array']]);
        $request->user()->update(['availability' => $data['availability']]);

        return response()->json(['availability' => $request->user()->fresh()->availability]);
    }
}
