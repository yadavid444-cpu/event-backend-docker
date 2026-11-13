<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::published()
            ->withCount('approvedRegistrations')
            ->when($request->query('search'), fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$s}%")
                ->orWhere('location', 'like', "%{$s}%")))
            ->when($request->query('when') !== 'all', fn ($q) => $q->where('start_at', '>=', now()->startOfDay()))
            ->orderBy('start_at')
            ->paginate(12);

        $events->getCollection()->transform(fn (Event $e) => $this->present($e));

        return $events;
    }

    public function show(Request $request, Event $event)
    {
        abort_unless($event->is_published, 404);

        $event->loadCount('approvedRegistrations');
        $data = $this->present($event);

        // Optional auth: if the client sent a token, include their registration status.
        $user = $this->optionalUser($request);
        $data['my_registration'] = $user
            ? $event->registrations()->where('user_id', $user->id)->first()
            : null;

        return $data;
    }

    private function present(Event $event): array
    {
        $approved = $event->approved_registrations_count ?? 0;

        return [
            ...$event->only(['id', 'title', 'description', 'location', 'start_at', 'end_at', 'capacity', 'image_url']),
            'approved_count' => $approved,
            'spots_left' => $event->capacity !== null ? max(0, $event->capacity - $approved) : null,
            'is_full' => $event->capacity !== null && $approved >= $event->capacity,
            'has_started' => $event->hasStarted(),
        ];
    }

    private function optionalUser(Request $request)
    {
        if ($token = $request->bearerToken()) {
            return PersonalAccessToken::findToken($token)?->tokenable;
        }

        return null;
    }
}
