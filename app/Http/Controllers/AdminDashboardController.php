<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'roleCounts' => User::query()->selectRaw('role, count(*) as total')->groupBy('role')->pluck('total', 'role')->all(),
            'statusCounts' => Appointment::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->all(),
            'recentActivity' => ActivityLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
