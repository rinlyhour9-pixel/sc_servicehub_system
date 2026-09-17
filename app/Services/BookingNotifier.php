<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\MobileNotification;

class BookingNotifier
{
    public static function send(User $recipient, string $title, string $body, Booking $booking): void
    {
        $recipient->notify(new MobileNotification($title, $body, ['booking_id' => $booking->id, 'status' => $booking->status]));
    }
}
