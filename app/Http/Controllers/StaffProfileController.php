<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StaffProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->staffProfile;

        return view('staff.profile', compact('user', 'profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'specialization' => ['required', 'string', 'max:120'],
            'biography' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $data['name'], 'phone' => $data['phone'] ?? null]);

        StaffProfile::updateOrCreate(
            ['user_id' => $user->id],
            ['specialization' => $data['specialization'], 'biography' => $data['biography'] ?? null],
        );

        ActivityLog::record($user, 'Updated the professional profile.');

        return back()->with('status', 'Profile saved.');
    }
}
