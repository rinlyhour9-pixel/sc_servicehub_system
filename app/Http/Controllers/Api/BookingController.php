<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingMedia;
use App\Models\Service;
use App\Services\BookingNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function services()
    {
        return response()->json(['data' => Service::where('is_active', true)->orderBy('name')->get()]);
    }

    public function availability(Request $request, Service $service)
    {
        abort_unless($service->is_active, 404);
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today']]);
        $day = Carbon::createFromFormat('Y-m-d', $data['date'])->startOfDay();
        $taken = Booking::whereDate('scheduled_at', $day)->whereNotIn('status', [Booking::STATUS_CANCELLED])->pluck('scheduled_at')->map(fn ($at) => Carbon::parse($at)->format('H:i'))->all();
        $slots = collect(range(9, 17))->map(fn ($hour) => $day->copy()->setTime($hour, 0))->filter(fn ($slot) => ! in_array($slot->format('H:i'), $taken, true) && $slot->isFuture())->values()->map(fn ($slot) => $slot->toIso8601String());

        return response()->json(['service_id' => $service->id, 'date' => $data['date'], 'duration_minutes' => $service->duration_minutes, 'slots' => $slots]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:8'],
            'photos.*' => ['image', 'max:5120'],
        ]);
        $service = Service::where('is_active', true)->findOrFail($data['service_id']);
        $booking = DB::transaction(function () use ($data, $request, $service) {
            $booking = Booking::create(array_merge(collect($data)->except(['photos'])->all(), ['client_id' => $request->user()->id, 'service_id' => $service->id]));
            $this->storePhotos($booking, $request, 'issue');

            return $booking;
        });
        BookingNotifier::send($request->user(), 'Booking received', 'Your booking has been created.', $booking);

        return (new BookingResource($booking->load('service', 'media')))->response()->setStatusCode(201);
    }

    public function index(Request $request)
    {
        $data = $request->validate(['status' => ['nullable', 'in:'.implode(',', Booking::STATUSES)]]);
        $query = Booking::with(['service', 'technician'])->where('client_id', $request->user()->id)->latest('scheduled_at');
        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }

        return BookingResource::collection($query->paginate(20));
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        return new BookingResource($booking->load(['service', 'client', 'technician', 'media', 'report']));
    }

    private function storePhotos(Booking $booking, Request $request, string $type): void
    {
        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store("bookings/{$booking->id}/{$type}", 'public');
            BookingMedia::create(['booking_id' => $booking->id, 'uploaded_by' => $request->user()->id, 'type' => $type, 'path' => $path, 'original_name' => $photo->getClientOriginalName(), 'mime_type' => $photo->getMimeType()]);
        }
    }
}
