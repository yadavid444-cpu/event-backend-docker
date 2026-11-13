@extends('admin.layout')
@section('live', '1')
@section('title', $event->title)

@section('content')
<div class="toolbar">
    <h1 style="margin:0">{{ $event->title }}</h1>
    <div class="actions">
        <a href="{{ route('admin.events.edit', $event) }}" class="btn gray">Edit</a>
        <a href="{{ route('admin.events.index') }}" class="btn gray">Back</a>
    </div>
</div>

<div class="card">
    @if ($event->image_url)
        <img src="{{ $event->image_url }}" alt="" style="max-width:320px;border-radius:8px;margin-bottom:12px">
    @endif
    <p><strong>When:</strong> {{ $event->start_at->format('d M Y, H:i') }}@if ($event->end_at) – {{ $event->end_at->format('d M Y, H:i') }}@endif</p>
    <p><strong>Where:</strong> {{ $event->location }}</p>
    <p><strong>Approved:</strong> {{ $event->approved_registrations_count }} / {{ $event->capacity ?? 'Unlimited' }}
        @if ($event->isFull()) <span class="badge rejected">Full</span> @endif</p>
    <p><strong>Status:</strong>
        @if ($event->is_published) <span class="badge approved">Published</span> @else <span class="badge draft">Draft</span> @endif</p>
    @if ($event->description)
        <p style="white-space:pre-line">{{ $event->description }}</p>
    @endif
</div>

<div class="card">
    <strong style="display:block;margin-bottom:12px">Join requests ({{ $registrations->count() }})</strong>
    @include('admin.registrations._table', ['registrations' => $registrations, 'showEvent' => false])
</div>
@endsection
