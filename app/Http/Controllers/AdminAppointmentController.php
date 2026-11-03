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

        $appointments = Appointment::query()
            ->with(['patient', 'staff'])
            ->when(in_array($status, Appointment::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest('appointment_date')
            ->latest('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('admin.appointments.index', compact('appointments', 'status'));
    }
}
