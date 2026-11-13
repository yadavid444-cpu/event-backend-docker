<?php

use Illuminate\Support\Facades\Broadcast;

// Each client listens to their own channel for approve/reject notifications.
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Admin dashboard: new / cancelled / reviewed join requests.
Broadcast::channel('admin', function ($user) {
    return (bool) $user->is_admin;
});
