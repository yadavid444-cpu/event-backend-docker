@extends('admin.layout')
@section('title', $event->exists ? 'Edit Event' : 'Create Event')

@section('content')
<h1>{{ $event->exists ? 'Edit Event' : 'Create Event' }}</h1>

<form class="card" method="POST" enctype="multipart/form-data"
      action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}">
    @csrf
    @if ($event->exists) @method('PUT') @endif

    <label for="title">Title *</label>
    <input id="title" type="text" name="title" value="{{ old('title', $event->title) }}" required>
    @error('title') <div class="err">{{ $message }}</div> @enderror

    <label for="description">Description</label>
    <textarea id="description" name="description">{{ old('description', $event->description) }}</textarea>
    @error('description') <div class="err">{{ $message }}</div> @enderror

    <label for="location">Location *</label>
    <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}" required>
    @error('location') <div class="err">{{ $message }}</div> @enderror

    <div class="row">
        <div>
            <label for="start_at">Start *</label>
            <input id="start_at" type="datetime-local" name="start_at" value="{{ old('start_at', $event->start_at?->format('Y-m-d\TH:i')) }}" required>
            @error('start_at') <div class="err">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="end_at">End</label>
            <input id="end_at" type="datetime-local" name="end_at" value="{{ old('end_at', $event->end_at?->format('Y-m-d\TH:i')) }}">
            @error('end_at') <div class="err">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="capacity">Capacity <span class="muted">(empty = unlimited)</span></label>
            <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $event->capacity) }}">
            @error('capacity') <div class="err">{{ $message }}</div> @enderror
        </div>
    </div>

    <label for="image">Cover image</label>
    @if ($event->image_url)
        <img src="{{ $event->image_url }}" alt="" style="max-width:240px;border-radius:8px;display:block;margin-bottom:8px">
    @endif
    <input id="image" type="file" name="image" accept="image/*">
    @error('image') <div class="err">{{ $message }}</div> @enderror

    <label style="font-weight:400">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $event->is_published) ? 'checked' : '' }}>
        Published (visible to clients)
    </label>

    <div class="actions" style="margin-top:20px">
        <button class="btn">{{ $event->exists ? 'Save changes' : 'Create event' }}</button>
        <a href="{{ $event->exists ? route('admin.events.show', $event) : route('admin.events.index') }}" class="btn gray">Cancel</a>
    </div>
</form>
@endsection
