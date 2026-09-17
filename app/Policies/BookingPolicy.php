<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->role === User::ROLE_ADMIN
            || $booking->client_id === $user->id
            || $booking->technician_id === $user->id;
    }
}
