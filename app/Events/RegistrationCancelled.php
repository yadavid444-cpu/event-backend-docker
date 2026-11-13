<?php

namespace App\Events;

use App\Models\EventRegistration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/** A client cancelled their pending request -> refresh admin lists live. */
class RegistrationCancelled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public string $userName, public string $eventTitle) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin')];
    }

    public function broadcastAs(): string
    {
        return 'registration.cancelled';
    }

    public function broadcastWith(): array
    {
        return [
            'user_name' => $this->userName,
            'event_title' => $this->eventTitle,
            'pending_count' => EventRegistration::where('status', EventRegistration::PENDING)->count(),
        ];
    }
}
