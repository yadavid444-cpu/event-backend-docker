<?php

namespace App\Events;

use App\Models\EventRegistration;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Admin approved / rejected a request.
 *  - private user channel: the client gets a live notification
 *  - public "events" channel: everyone's spot counts refresh
 *  - private admin channel: other open admin tabs refresh
 */
class RegistrationStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public EventRegistration $registration) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->registration->user_id),
            new Channel('events'),
            new PrivateChannel('admin'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'registration.status';
    }

    public function broadcastWith(): array
    {
        $r = $this->registration->loadMissing('event');

        return [
            'id' => $r->id,
            'event_id' => $r->event_id,
            'event_title' => $r->event->title,
            'status' => $r->status,
            'pending_count' => EventRegistration::where('status', EventRegistration::PENDING)->count(),
        ];
    }
}
