@extends('layouts.app')

@section('title', 'My appointments')

@section('content')
<h1 class="h3 fw-bold mb-4">My appointments</h1>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Professional</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->appointment_date->format('d M Y') }}</td>
                        <td>{{ $appointment->time_range }}</td>
                        <td>{{ $appointment->staff->name }}</td>
                        <td><x-status-badge :status="$appointment->status" /></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('patient.appointments.show', $appointment) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-secondary p-4">You have no appointments yet. <a href="{{ route('professionals.index') }}">Find a professional</a> to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $appointments->links() }}</div>
@endsection
