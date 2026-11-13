<?php

namespace App\Http\Controllers\Admin;

use App\Events\RegistrationStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $registrations = EventRegistration::with(['event', 'user'])
            ->when(in_array($status, ['pending', 'approved', 'rejected']), fn ($q) => $q->where('status', $status))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.registrations.index', compact('registrations', 'status'));
    }

    public function approve(Request $request, EventRegistration $registration)
    {
        $event = $registration->event;

        if ($registration->status !== EventRegistration::APPROVED && $event->isFull()) {
            return back()->with('error', 'Cannot approve: "'.$event->title.'" is already full.');
        }

        $this->review($request, $registration, EventRegistration::APPROVED);

        return back()->with('success', 'Approved '.$registration->user->name.' for "'.$event->title.'".');
    }

    public function reject(Request $request, EventRegistration $registration)
    {
        $this->review($request, $registration, EventRegistration::REJECTED);

        return back()->with('success', 'Rejected '.$registration->user->name.' for "'.$registration->event->title.'".');
    }

    private function review(Request $request, EventRegistration $registration, string $status): void
    {
        $registration->update([
            'status' => $status,
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        rescue(fn () => broadcast(new RegistrationStatusChanged($registration)));
    }
}
