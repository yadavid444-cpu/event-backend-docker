<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · {{ config('app.name') }}</title>
    <style>
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background:#111827; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:16px; }
        .box { background:#fff; border-radius:12px; padding:32px; width:100%; max-width:380px; }
        h1 { margin:0 0 6px; font-size:22px; } p { margin:0 0 20px; color:#6b7280; }
        label { display:block; font-weight:600; font-size:14px; margin:14px 0 6px; }
        input[type=email], input[type=password] { width:100%; box-sizing:border-box; padding:10px 12px; border:1px solid #d1d5db; border-radius:6px; font:inherit; }
        button { width:100%; margin-top:20px; padding:11px; border:0; border-radius:6px; background:#4f46e5; color:#fff; font-size:15px; cursor:pointer; }
        .err { color:#dc2626; font-size:13px; margin-top:6px; }
    </style>
</head>
<body>
<form class="box" method="POST" action="{{ route('admin.login.submit') }}">
    @csrf
    <h1>🎟 Event Admin</h1>
    <p>Sign in to manage events.</p>
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
    @error('email') <div class="err">{{ $message }}</div> @enderror
    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>
    <label style="font-weight:400"><input type="checkbox" name="remember"> Remember me</label>
    <button type="submit">Login</button>
</form>
</body>
</html>
