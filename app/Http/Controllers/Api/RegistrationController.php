<?php

namespace App\Http\Controllers\Api;

use App\Events\RegistrationCancelled;
use App\Events\RegistrationCreated;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /** Client's own registrations with event info and status. */
    public function index(Request $request)
    {
        return $request->user()->registrations()
            ->with('event:id,title,location,start_at,end_at,image')
            ->latest()
            ->get();
    }

    /** Client joins an event -> registration starts as "pending". */
    public function store(Request $request, Event $event)
    {
        abort_unless($event->is_published, 404);

        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($event->hasStarted()) {
            return response()->json(['message' => 'This event has already started.'], 422);
        }
        if ($event->isFull()) {
            return response()->json(['message' => 'This event is full.'], 422);
        }
        if ($event->registrations()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'You have already joined this event.'], 422);
        }

        $registration = $event->registrations()->create([
            ...$data,
            'phone' => $data['phone'] ?? $request->user()->phone,
            'user_id' => $request->user()->id,
            'status' => EventRegistration::PENDING,
        ]);

        rescue(fn () => broadcast(new RegistrationCreated($registration)));

        return response()->json([
            'message' => 'Request sent! Waiting for admin approval.',
            'registration' => $registration,
        ], 201);
    }

    /** Client cancels a pending request. */
    public function destroy(Request $request, EventRegistration $registration)
    {
        abort_unless($registration->user_id === $request->user()->id, 403);

        if ($registration->status !== EventRegistration::PENDING) {
            return response()->json(['message' => 'Only pending requests can be cancelled.'], 422);
        }

        $registration->delete();

        rescue(fn () => broadcast(new RegistrationCancelled($request->user()->name, $registration->event->title)));

        return response()->json(['message' => 'Request cancelled.']);
    }
}
