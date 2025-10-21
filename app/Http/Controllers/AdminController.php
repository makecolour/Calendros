<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Event;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function dashboard(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_calendars' => Calendar::count(),
            'total_events' => Event::count(),
            'total_invites' => Invite::count(),
            'pending_invites' => Invite::where('status', 'pending')->count(),
            'accepted_invites' => Invite::where('status', 'accepted')->count(),
            'rejected_invites' => Invite::where('status', 'rejected')->count(),
        ];

        // Recent users (last 10)
        $recentUsers = User::latest()->take(10)->get(['id', 'name', 'email', 'is_admin', 'created_at']);

        // Recent events (last 10)
        $recentEvents = Event::with(['calendar.user'])
            ->latest()
            ->take(10)
            ->get(['id', 'title', 'calendar_id', 'start_time', 'end_time', 'created_at']);

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentEvents'));
    }
}
