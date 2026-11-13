@if ($registrations->isEmpty())
    <p class="muted">No requests here.</p>
@else
<table>
    <thead>
    <tr>
        <th>Client</th>
        @if ($showEvent ?? false) <th>Event</th> @endif
        <th>Phone</th>
        <th>Note</th>
        <th>Requested</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($registrations as $r)
        <tr>
            <td>{{ $r->user->name }}<br><span class="muted">{{ $r->user->email }}</span></td>
            @if ($showEvent ?? false)
                <td><a href="{{ route('admin.events.show', $r->event) }}">{{ $r->event->title }}</a></td>
            @endif
            <td>{{ $r->phone ?: '—' }}</td>
            <td>{{ \Illuminate\Support\Str::limit($r->note, 60) ?: '—' }}</td>
            <td>{{ $r->created_at->diffForHumans() }}</td>
            <td><span class="badge {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
            <td>
                <div class="actions">
                    @if ($r->status !== 'approved')
                        <form method="POST" action="{{ route('admin.registrations.approve', $r) }}">
                            @csrf @method('PATCH')
                            <button class="btn sm green">Approve</button>
                        </form>
                    @endif
                    @if ($r->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.registrations.reject', $r) }}">
                            @csrf @method('PATCH')
                            <button class="btn sm red">Reject</button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif
