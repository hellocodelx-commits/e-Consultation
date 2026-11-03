<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\ConsultationDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $appointments = Appointment::with('patient')
            ->where('staff_id', auth()->id())
            ->when(in_array($status, Appointment::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('staff.appointments.index', compact('appointments', 'status'));
    }

    public function show(Appointment $appointment): View
    {
        abort_unless($appointment->staff_id === auth()->id(), 403);

        $appointment->load(['patient', 'consultation']);

        return view('staff.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->staff_id === auth()->id(), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in([
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_DECLINED,
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_CANCELLED,
            ])],
        ]);

        if (! $appointment->canTransitionTo($data['status'])) {
            return back()->with('error', 'That status change is not allowed.');
        }

        DB::transaction(fn () => $appointment->transitionTo($data['status']));

        ActivityLog::record(auth()->user(), "Set appointment #{$appointment->id} to {$data['status']}.");

        return back()->with('status', 'Appointment updated.');
    }

    public function saveConsultation(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($appointment->staff_id === auth()->id(), 403);

        if ($appointment->status !== Appointment::STATUS_CONFIRMED) {
            return back()->with('error', 'Consultation details can be added once the appointment is confirmed.');
        }

        $data = $request->validate([
            'meeting_link' => ['required', 'url:https', 'max:500'],
            'meeting_id' => ['nullable', 'string', 'max:50'],
            'passcode' => ['nullable', 'string', 'max:50'],
            'instructions' => ['nullable', 'string', 'max:1000'],
        ]);

        ConsultationDetail::updateOrCreate(['appointment_id' => $appointment->id], $data);

        ActivityLog::record(auth()->user(), "Saved consultation details for appointment #{$appointment->id}.");

        return back()->with('status', 'Consultation details saved.');
    }
}
