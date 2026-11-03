@extends('layouts.app')

@section('title', 'Appointment details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a class="small" href="{{ route('patient.appointments.index') }}">&larr; Back to my appointments</a>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h4 fw-bold mb-0">Appointment with {{ $appointment->staff->name }}</h1>
                    <x-status-badge :status="$appointment->status" />
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-4">Date</dt>
                    <dd class="col-sm-8">{{ $appointment->appointment_date->format('l, d M Y') }}</dd>
                    <dt class="col-sm-4">Time</dt>
                    <dd class="col-sm-8">{{ $appointment->time_range }}</dd>
                    <dt class="col-sm-4">Specialisation</dt>
                    <dd class="col-sm-8">{{ $appointment->staff->staffProfile->specialization ?? 'Not listed' }}</dd>
                    <dt class="col-sm-4">Reason</dt>
                    <dd class="col-sm-8">{{ $appointment->reason ?: 'Not provided' }}</dd>
                </dl>

                @if (in_array($appointment->status, ['confirmed', 'completed'], true))
                    <hr>
                    <h2 class="h5 fw-semibold">Online consultation</h2>
                    @if ($appointment->consultation)
                        <p class="mb-2">Meeting link: <a href="{{ $appointment->consultation->meeting_link }}" target="_blank" rel="noopener">Join on Zoom</a></p>
                        @if ($appointment->consultation->meeting_id)
                            <p class="mb-1">Meeting ID: {{ $appointment->consultation->meeting_id }}</p>
                        @endif
                        @if ($appointment->consultation->passcode)
                            <p class="mb-1">Passcode: {{ $appointment->consultation->passcode }}</p>
                        @endif
                        @if ($appointment->consultation->instructions)
                            <p class="text-secondary mt-2 mb-0">{{ $appointment->consultation->instructions }}</p>
                        @endif
                    @else
                        <p class="text-secondary mb-0">The meeting details have not been added yet. Check back before your appointment.</p>
                    @endif
                @endif

                @if ($appointment->canTransitionTo('cancelled'))
                    <hr>
                    <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}" onsubmit="return confirm('Cancel this appointment?');">
                        @csrf
                        <button class="btn btn-outline-danger" type="submit">Cancel appointment</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
