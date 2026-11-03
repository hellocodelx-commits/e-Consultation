<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $roleCounts = User::selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role');
        $statusCounts = Appointment::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $recentActivity = ActivityLog::with('user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('roleCounts', 'statusCounts', 'recentActivity'));
    }
}
