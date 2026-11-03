<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use App\Models\StaffProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    public function index(): View
    {
        $staffId = auth()->id();

        $counts = [
            'pending' => \App\Models\Appointment::where('staff_id', $staffId)->where('status', 'pending')->count(),
            'confirmed' => \App\Models\Appointment::where('staff_id', $staffId)->where('status', 'confirmed')->count(),
            'openSlots' => AvailabilitySlot::where('staff_id', $staffId)->open()->upcoming()->count(),
        ];

        $upcoming = \App\Models\Appointment::with('patient')
            ->where('staff_id', $staffId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $hasProfile = StaffProfile::where('user_id', $staffId)->exists();

        return view('staff.dashboard', compact('counts', 'upcoming', 'hasProfile'));
    }
}
