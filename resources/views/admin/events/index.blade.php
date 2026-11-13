@extends('admin.layout')
@section('live', '1')
@section('title', 'Events')

@section('content')
<div class="toolbar">
    <h1 style="margin:0">Events</h1>
    <a href="{{ route('admin.events.create') }}" class="btn">+ Create Event</a>
</div>

<div class="card">
    @if ($events->isEmpty())
        <p class="muted">No events yet. Create your first one.</p>
    @else
    <table>
        <thead>
        <tr><th>Title</th><th>Date</th><th>Location</th><th>Approved / Capacity</th><th>Pending</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        @foreach ($events as $event)
            <tr>
                <td><span class="thumb" style="{{ $event->image_url ? 'background-image:url('.$event->image_url.')' : '' }}"></span><a href="{{ route('admin.events.show', $event) }}"><strong>{{ $event->title }}</strong></a></td>
                <td>{{ $event->start_at->format('d M Y, H:i') }}</td>
                <td>{{ $event->location }}</td>
                <td>{{ $event->approved_registrations_count }} / {{ $event->capacity ?? '∞' }}</td>
                <td>{{ $event->pending_registrations_count }}</td>
                <td>
                    @if ($event->is_published) <span class="badge approved">Published</span>
                    @else <span class="badge draft">Draft</span> @endif
                </td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn sm gray">Edit</a>
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event and all its join requests?')">
                            @csrf @method('DELETE')
                            <button class="btn sm red">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('admin._pager', ['paginator' => $events])
    @endif
</div>
@endsection
