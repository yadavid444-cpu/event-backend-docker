<?php

namespace App\Http\Controllers\Admin;

use App\Events\EventsChanged;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount([
            'registrations',
            'approvedRegistrations',
            'registrations as pending_registrations_count' => fn ($q) => $q->where('status', EventRegistration::PENDING),
        ])->orderByDesc('start_at')->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.form', ['event' => new Event(['is_published' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($data);
        rescue(fn () => broadcast(new EventsChanged($event->id, 'created', $event->title)));

        return redirect()->route('admin.events.show', $event)->with('success', 'Event created.');
    }

    public function show(Event $event)
    {
        $event->loadCount('approvedRegistrations');
        $registrations = $event->registrations()->with('user')->latest()->get();

        return view('admin.events.show', compact('event', 'registrations'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);
        rescue(fn () => broadcast(new EventsChanged($event->id, 'updated', $event->title)));

        return redirect()->route('admin.events.show', $event)->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        $id = $event->id;
        $title = $event->title;
        $event->delete();
        rescue(fn () => broadcast(new EventsChanged($id, 'deleted', $title)));

        return redirect()->route('admin.events.index')->with('success', 'Event deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);
        unset($data['image']);
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }
}
