<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        $isAdmin = $viewer?->role === User::ROLE_ADMIN;
        $isTechnician = $viewer?->role === User::ROLE_TECHNICIAN;

        return [
            'id' => $this->id,
            'service' => ['id' => $this->service?->id, 'name' => $this->service?->name, 'duration_minutes' => $this->service?->duration_minutes],
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'status' => $this->status,
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'client' => $this->when($isAdmin || $isTechnician, fn () => [
                'id' => $this->client?->id, 'name' => $this->client?->name, 'phone' => $this->client?->phone,
                'email' => $isAdmin ? $this->client?->email : null,
            ]),
            'technician' => $this->when($isAdmin || $viewer?->id === $this->client_id, fn () => $this->technician ? [
                'id' => $this->technician->id, 'name' => $this->technician->name,
            ] : null),
            'media' => $this->whenLoaded('media', fn () => $this->media->map(fn ($media) => [
                'id' => $media->id, 'type' => $media->type, 'url' => Storage::disk('public')->url($media->path),
            ])),
            'report' => $this->whenLoaded('report', fn () => $this->report ? [
                'report' => $this->report->report, 'metadata' => $this->report->metadata, 'completed_by' => $this->report->technician_id,
            ] : null),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
