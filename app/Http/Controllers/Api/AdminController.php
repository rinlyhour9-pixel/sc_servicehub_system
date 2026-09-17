<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingNotifier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json(['data' => [
            'bookings' => [
                'total' => Booking::count(), 'pending' => Booking::where('status', 'pending')->count(),
                'assigned' => Booking::where('status', 'assigned')->count(), 'in_progress' => Booking::where('status', 'in_progress')->count(),
                'completed_today' => Booking::where('status', 'completed')->whereDate('completed_at', today())->count(),
            ],
            'customers' => User::where('role', User::ROLE_CLIENT)->count(),
            'technicians' => User::where('role', User::ROLE_TECHNICIAN)->where('is_active', true)->count(),
        ]]);
    }

    public function bookings(Request $request)
    {
        $data = $request->validate(['status' => ['nullable', 'in:'.implode(',', Booking::STATUSES)], 'date' => ['nullable', 'date_format:Y-m-d']]);
        $query = Booking::with(['service', 'client', 'technician'])->latest('scheduled_at');
        if (! empty($data['status'])) {
            $query->where('status', $data['status']);
        }
        if (! empty($data['date'])) {
            $query->whereDate('scheduled_at', $data['date']);
        }

        return BookingResource::collection($query->paginate(30));
    }

    public function assign(Request $request, Booking $booking)
    {
        $data = $request->validate(['technician_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', User::ROLE_TECHNICIAN)->where('is_active', true))]]);
        if (in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED], true)) {
            abort(422, 'Closed bookings cannot be assigned.');
        }
        $booking->update(['technician_id' => $data['technician_id'], 'status' => Booking::STATUS_ASSIGNED]);
        $booking->load(['client', 'technician', 'service']);
        BookingNotifier::send($booking->client, 'Technician assigned', 'A technician has been assigned to your booking.', $booking);
        BookingNotifier::send($booking->technician, 'New job assigned', 'You have been assigned a new booking.', $booking);

        return new BookingResource($booking);
    }

    public function customers(Request $request)
    {
        $query = User::where('role', User::ROLE_CLIENT)->orderBy('name');
        if ($request->filled('search')) {
            $request->validate(['search' => ['string', 'max:100']]);
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->search.'%')->orWhere('email', 'like', '%'.$request->search.'%')->orWhere('phone', 'like', '%'.$request->search.'%'));
        }

        return response()->json($query->paginate(30));
    }

    public function createCustomer(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'], 'password' => ['required', 'confirmed', 'min:8'],
        ]);
        $customer = User::create(array_merge($data, ['role' => User::ROLE_CLIENT]));

        return response()->json(['data' => $this->person($customer)], 201);
    }

    public function customer(User $customer)
    {
        abort_unless($customer->role === User::ROLE_CLIENT, 404);

        return response()->json(['data' => array_merge($this->person($customer), ['bookings_count' => $customer->bookings()->count(), 'recent_bookings' => BookingResource::collection($customer->bookings()->with('service')->latest()->take(5)->get())])]);
    }

    public function technicians()
    {
        return response()->json(User::where('role', User::ROLE_TECHNICIAN)->orderBy('name')->paginate(30));
    }

    public function technician(User $technician)
    {
        abort_unless($technician->role === User::ROLE_TECHNICIAN, 404);

        return response()->json(['data' => array_merge($this->person($technician), ['availability' => $technician->availability, 'assigned_bookings_count' => $technician->assignedBookings()->whereIn('status', ['assigned', 'in_progress'])->count()])]);
    }

    private function person(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'role' => $user->role, 'is_active' => $user->is_active, 'created_at' => $user->created_at?->toIso8601String()];
    }
}
