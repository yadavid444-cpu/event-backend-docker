@extends('admin.layout')
@section('live', '1')
@section('title', 'Dashboard')

@section('content')
<h1>Dashboard</h1>

<div class="grid" style="margin-bottom:20px">
    <div class="card stat"><div class="n">{{ $stats['events'] }}</div><div class="l">Total events</div></div>
    <div class="card stat"><div class="n">{{ $stats['upcoming'] }}</div><div class="l">Upcoming events</div></div>
    <div class="card stat"><div class="n">{{ $stats['pending'] }}</div><div class="l">Pending join requests</div></div>
    <div class="card stat"><div class="n">{{ $stats['clients'] }}</div><div class="l">Registered clients</div></div>
</div>

<div class="card">
    <div class="toolbar">
        <strong>Latest pending requests</strong>
        <a href="{{ route('admin.registrations.index') }}">View all →</a>
    </div>
    @include('admin.registrations._table', ['registrations' => $latestPending, 'showEvent' => true])
</div>
@endsection
