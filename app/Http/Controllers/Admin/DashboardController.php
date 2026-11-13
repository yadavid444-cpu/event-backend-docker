<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                'events' => Event::count(),
                'upcoming' => Event::where('start_at', '>=', now())->count(),
                'pending' => EventRegistration::where('status', EventRegistration::PENDING)->count(),
                'clients' => User::where('is_admin', false)->count(),
            ],
            'latestPending' => EventRegistration::with(['event', 'user'])
                ->where('status', EventRegistration::PENDING)
                ->latest()->take(8)->get(),
        ]);
    }
}
