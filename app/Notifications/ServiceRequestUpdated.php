<?php

namespace App\Notifications;

use App\Models\ServiceRequest;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceRequestUpdated extends Notification
{
    use Queueable;

    public function __construct(private ServiceRequest $serviceRequest, private string $message) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof Customer ? ['mail'] : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Service update: {$this->serviceRequest->ticket_number}")
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->line("Service: {$this->serviceRequest->title}")
            ->line('Please contact us if you have any questions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'ticket_number' => $this->serviceRequest->ticket_number,
            'service_request_id' => $this->serviceRequest->id,
            'url' => route('service-requests.show', $this->serviceRequest),
        ];
    }
}
