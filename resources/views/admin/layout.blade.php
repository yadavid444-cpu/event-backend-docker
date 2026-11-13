<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
    <style>
        :root { --bg:#f4f5f7; --card:#fff; --text:#1f2430; --muted:#6b7280; --line:#e5e7eb;
                --primary:#4f46e5; --green:#16a34a; --red:#dc2626; --amber:#d97706; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background:var(--bg); color:var(--text); }
        a { color: var(--primary); text-decoration: none; }
        .shell { display:flex; min-height:100vh; }
        aside { width:220px; background:#111827; color:#d1d5db; padding:20px 0; flex-shrink:0; }
        aside .brand { color:#fff; font-weight:700; font-size:18px; padding:0 20px 20px; }
        aside a { display:block; color:#d1d5db; padding:10px 20px; }
        aside a.active, aside a:hover { background:#1f2937; color:#fff; }
        aside form { padding: 10px 20px; }
        main { flex:1; padding:28px; max-width:1200px; }
        h1 { margin:0 0 20px; font-size:24px; }
        .card { background:var(--card); border:1px solid var(--line); border-radius:10px; padding:20px; margin-bottom:20px; }
        .grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap:16px; }
        .stat .n { font-size:28px; font-weight:700; } .stat .l { color:var(--muted); font-size:14px; }
        table { width:100%; border-collapse:collapse; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); font-size:14px; vertical-align:middle; }
        th { color:var(--muted); font-weight:600; }
        .btn { display:inline-block; border:0; border-radius:6px; padding:7px 14px; font-size:14px; cursor:pointer; background:var(--primary); color:#fff; }
        .btn.sm { padding:4px 10px; font-size:13px; }
        .btn.green { background:var(--green); } .btn.red { background:var(--red); } .btn.gray { background:#6b7280; }
        .btn.link { background:none; color:#d1d5db; padding:0; }
        .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; font-weight:600; }
        .badge.pending { background:#fef3c7; color:var(--amber); }
        .badge.approved { background:#dcfce7; color:var(--green); }
        .badge.rejected { background:#fee2e2; color:var(--red); }
        .badge.draft { background:#e5e7eb; color:#374151; }
        .alert { padding:12px 16px; border-radius:8px; margin-bottom:16px; }
        .alert.success { background:#dcfce7; color:#166534; } .alert.error { background:#fee2e2; color:#991b1b; }
        label { display:block; font-weight:600; font-size:14px; margin:14px 0 6px; }
        input[type=text], input[type=email], input[type=password], input[type=number], input[type=datetime-local], textarea {
            width:100%; padding:9px 11px; border:1px solid #d1d5db; border-radius:6px; font:inherit; }
        textarea { min-height:110px; }
        .err { color:var(--red); font-size:13px; margin-top:4px; }
        .row { display:flex; gap:16px; } .row > div { flex:1; }
        .actions { display:flex; gap:6px; align-items:center; flex-wrap:wrap; }
        .actions form { margin:0; }
        .muted { color:var(--muted); }
        .toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .tabs a { padding:6px 12px; border-radius:6px; margin-right:4px; color:var(--text); }
        .tabs a.active { background:var(--primary); color:#fff; }
        nav[role=navigation] svg { width:16px; }
        .nav-badge { background:#ef4444; color:#fff; border-radius:999px; font-size:12px; font-weight:700; padding:1px 8px; margin-left:6px; }
        .nav-badge[hidden] { display:none; }
        .live-status { padding:10px 20px; font-size:13px; color:#9ca3af; display:flex; align-items:center; gap:6px; }
        .dot { width:8px; height:8px; border-radius:50%; background:#6b7280; display:inline-block; }
        .dot.on { background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,.25); }
        #toasts { position:fixed; right:20px; bottom:20px; display:flex; flex-direction:column; gap:10px; z-index:100; max-width:360px; }
        .toast { background:#111827; color:#fff; padding:12px 16px; border-radius:10px; box-shadow:0 10px 25px rgba(0,0,0,.2);
                 font-size:14px; transition: opacity .5s, transform .5s; animation: toast-in .3s ease-out; }
        .toast.new { background:#4f46e5; }
        .toast.hide { opacity:0; transform:translateY(10px); }
        @keyframes toast-in { from { opacity:0; transform:translateY(10px); } }
        .thumb { width:64px; height:36px; border-radius:6px; background-size:cover; background-position:center; background-color:#e5e7eb; display:inline-block; vertical-align:middle; margin-right:10px; }
        @media (max-width: 760px) { .shell { flex-direction:column; } aside { width:auto; } main { padding:16px; } .row { flex-direction:column; gap:0; } }
    </style>
</head>
<body>
<div class="shell">
    <aside>
        <div class="brand">🎟 Event Admin</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">Events</a>
        @php($pendingCount = \App\Models\EventRegistration::where('status', 'pending')->count())
        <a href="{{ route('admin.registrations.index') }}" class="{{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
            Join Requests <span id="pending-badge" class="nav-badge" @if (! $pendingCount) hidden @endif>{{ $pendingCount }}</span>
        </a>
        <div class="live-status"><span id="live-dot" class="dot"></span> <span id="live-text">Connecting…</span></div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button class="btn link">Logout ({{ auth()->user()->name }})</button>
        </form>
    </aside>
    <main>
        @if (session('success')) <div class="alert success">{{ session('success') }}</div> @endif
        @if (session('error')) <div class="alert error">{{ session('error') }}</div> @endif
        <div id="live-region" data-live="@yield('live', '0')">
            @yield('content')
        </div>
    </main>
</div>
<div id="toasts"></div>

<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script>
(function () {
    const csrf = @json(csrf_token());
    const pusher = new Pusher(@json(config('broadcasting.connections.reverb.key')), {
        cluster: 'mt1',
        wsHost: @json(config('broadcasting.connections.reverb.options.host')),
        wsPort: @json((int) config('broadcasting.connections.reverb.options.port')),
        wssPort: @json((int) config('broadcasting.connections.reverb.options.port')),
        forceTLS: @json(config('broadcasting.connections.reverb.options.scheme') === 'https'),
        enabledTransports: ['ws', 'wss'],
        channelAuthorization: { endpoint: '/broadcasting/auth', headers: { 'X-CSRF-TOKEN': csrf } },
    });

    const dot = document.getElementById('live-dot');
    const text = document.getElementById('live-text');
    pusher.connection.bind('state_change', ({ current }) => {
        const on = current === 'connected';
        dot.classList.toggle('on', on);
        text.textContent = on ? 'Live' : (current === 'connecting' ? 'Connecting…' : 'Offline');
    });

    function toast(message, kind = 'info') {
        const el = document.createElement('div');
        el.className = 'toast ' + kind;
        el.textContent = message;
        document.getElementById('toasts').appendChild(el);
        setTimeout(() => el.classList.add('hide'), 5000);
        setTimeout(() => el.remove(), 5600);
    }

    function setPending(n) {
        const b = document.getElementById('pending-badge');
        b.textContent = n;
        b.hidden = !n;
        document.title = (n ? '(' + n + ') ' : '') + document.title.replace(/^\(\d+\)\s*/, '');
    }

    // Re-render the current page's content without a full reload (only on list pages).
    let refreshing = null;
    function refresh() {
        const region = document.getElementById('live-region');
        if (region.dataset.live !== '1') return;
        clearTimeout(refreshing);
        refreshing = setTimeout(async () => {
            const html = await (await fetch(location.href, { headers: { 'X-Requested-With': 'fetch' } })).text();
            const fresh = new DOMParser().parseFromString(html, 'text/html').getElementById('live-region');
            if (fresh) region.innerHTML = fresh.innerHTML;
        }, 250);
    }

    pusher.subscribe('private-admin')
        .bind('registration.created', (e) => {
            toast('🔔 New join request: ' + e.user_name + ' → "' + e.event_title + '"', 'new');
            setPending(e.pending_count);
            refresh();
        })
        .bind('registration.cancelled', (e) => {
            toast(e.user_name + ' cancelled their request for "' + e.event_title + '"');
            setPending(e.pending_count);
            refresh();
        })
        .bind('registration.status', (e) => {
            setPending(e.pending_count);
            refresh();
        });
})();
</script>
</body>
</html>
