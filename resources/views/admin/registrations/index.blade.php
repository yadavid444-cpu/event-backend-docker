@extends('admin.layout')
@section('live', '1')
@section('title', 'Join Requests')

@section('content')
<h1>Join Requests</h1>

<div class="card">
    <div class="tabs" style="margin-bottom:16px">
        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $key => $label)
            <a href="{{ route('admin.registrations.index', ['status' => $key]) }}" class="{{ $status === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>
    @include('admin.registrations._table', ['registrations' => $registrations, 'showEvent' => true])
    @include('admin._pager', ['paginator' => $registrations])
</div>
@endsection
