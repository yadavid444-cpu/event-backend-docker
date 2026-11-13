<?php

namespace App\Events;

use App\Models\EventRegistration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/** A client requested to join an event -> notify admins live. */
class RegistrationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public EventRegistration $registration) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin')];
    }

    public function broadcastAs(): string
    {
        return 'registration.created';
    }

    public function broadcastWith(): array
    {
        $r = $this->registration->loadMissing(['user', 'event']);

        return [
            'id' => $r->id,
            'user_name' => $r->user->name,
            'event_title' => $r->event->title,
            'pending_count' => EventRegistration::where('status', EventRegistration::PENDING)->count(),
        ];
    }
}
