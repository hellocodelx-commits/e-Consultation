<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\AvailabilitySlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PatientAppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::with('staff.staffProfile')
            ->where('patient_id', auth()->id())
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(10);

        return view('patient.appointments.index', compact('appointments'));
    }

    public function store(Request $request, AvailabilitySlot $slot): RedirectResponse
    {
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $appointment = DB::transaction(function () use ($slot, $data) {
            $locked = AvailabilitySlot::whereKey($slot->id)->lockForUpdate()->first();

            if (! $locked || $locked->status !== AvailabilitySlot::STATUS_OPEN || $locked->date->lt(today())) {
                return null;
            }

            $appointment = Appointment::create([
                'patient_id' => auth()->id(),
                'staff_id' => $locked->staff_id,
                'availability_slot_id' => $locked->id,
                'appointment_date' => $locked->date,
                'start_time' => $locked->start_time,
                'end_time' => $locked->end_time,
                'reason' => $data['reason'] ?? null,
                'status' => Appointment::STATUS_PENDING,
            ]);

            $locked->update(['status' => AvailabilitySlot::STATUS_BOOKED]);

            return $appointment;
        });

        if (! $appointment) {
            return back()->with('error', 'That time is no longer available. Please choose another slot.');
        }

        ActivityLog::record(auth()->user(), "Requested an appointment on {$appointment->appointment_date->format('d M Y')}.");

        return redirect()->route('patient.appointments.show', $appointment)
            ->with('status', 'Your request has been sent. It will show as confirmed once the professional accepts it.');
    }

    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->patient_id === auth()->id(), 403);

        $appointment->load(['staff.staffProfile', 'consultation']);

        return view('patient.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->patient_id === auth()->id(), 403);

        if (! $appointment->canTransitionTo(Appointment::STATUS_CANCELLED)) {
            return back()->with('error', 'This appointment can no longer be cancelled.');
        }

        DB::transaction(fn () => $appointment->transitionTo(Appointment::STATUS_CANCELLED));

        ActivityLog::record(auth()->user(), "Cancelled appointment #{$appointment->id}.");

        return redirect()->route('patient.appointments.index')->with('status', 'Appointment cancelled.');
    }
}
