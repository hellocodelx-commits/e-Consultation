<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AvailabilitySlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffAvailabilityController extends Controller
{
    public function index(): View
    {
        $slots = auth()->user()->availabilitySlots()
            ->upcoming()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('staff.availability.index', compact('slots'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
        ]);

        if ($data['end_time'] <= $data['start_time']) {
            return back()->withInput()->withErrors(['end_time' => 'The end time must be after the start time.']);
        }

        $staff = auth()->user();

        $overlaps = AvailabilitySlot::where('staff_id', $staff->id)
            ->whereDate('date', $data['date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($overlaps) {
            return back()->withInput()->with('error', 'This time overlaps an existing slot on the same day.');
        }

        AvailabilitySlot::create([
            'staff_id' => $staff->id,
            'date' => $data['date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => AvailabilitySlot::STATUS_OPEN,
        ]);

        ActivityLog::record($staff, "Added an availability slot on {$data['date']}.");

        return back()->with('status', 'Time slot added.');
    }

    public function destroy(AvailabilitySlot $slot): RedirectResponse
    {
        abort_unless($slot->staff_id === auth()->id(), 403);

        if ($slot->status === AvailabilitySlot::STATUS_BOOKED) {
            return back()->with('error', 'Booked slots cannot be removed. Decline or cancel the appointment first.');
        }

        $slot->delete();

        ActivityLog::record(auth()->user(), 'Removed an availability slot.');

        return back()->with('status', 'Time slot removed.');
    }
}
