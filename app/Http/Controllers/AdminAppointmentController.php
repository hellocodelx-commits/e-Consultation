<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $appointments = Appointment::with(['patient', 'staff'])
            ->when(in_array($status, Appointment::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'status'));
    }
}
