<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/** Admin created / updated / deleted an event -> client lists refresh live. */
class EventsChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public int $eventId, public string $action, public string $title) {}

    public function broadcastOn(): array
    {
        return [new Channel('events')];
    }

    public function broadcastAs(): string
    {
        return 'events.changed';
    }

    public function broadcastWith(): array
    {
        return ['event_id' => $this->eventId, 'action' => $this->action, 'title' => $this->title];
    }
}
