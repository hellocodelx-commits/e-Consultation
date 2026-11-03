@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold mb-0">Appointments</h1>
    <form method="GET" action="{{ route('staff.appointments.index') }}" class="d-flex gap-2">
        <select name="status" class="form-select" aria-label="Filter by status">
            <option value="">All statuses</option>
            @foreach (\App\Models\Appointment::STATUSES as $option)
                <option value="{{ $option }}" @selected($status === $option)>{{ ucfirst($option) }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-primary" type="submit">Filter</button>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Patient</th><th scope="col">Status</th><th scope="col"></th></tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->appointment_date->format('d M Y') }}</td>
                        <td>{{ $appointment->time_range }}</td>
                        <td>{{ $appointment->patient->name }}</td>
                        <td><x-status-badge :status="$appointment->status" /></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('staff.appointments.show', $appointment) }}">Review</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-secondary p-4">No appointments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $appointments->links() }}</div>
@endsection
