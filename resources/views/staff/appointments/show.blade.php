@extends('layouts.app')

@section('title', 'Review appointment')

@section('content')
<div class="row justify-content-center g-4">
    <div class="col-lg-6">
        <a class="small" href="{{ route('staff.appointments.index') }}">&larr; Back to appointments</a>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h4 fw-bold mb-0">{{ $appointment->patient->name }}</h1>
                    <x-status-badge :status="$appointment->status" />
                </div>
                <dl class="row mb-4">
                    <dt class="col-sm-4">Date</dt>
                    <dd class="col-sm-8">{{ $appointment->appointment_date->format('l, d M Y') }}</dd>
                    <dt class="col-sm-4">Time</dt>
                    <dd class="col-sm-8">{{ $appointment->time_range }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $appointment->patient->phone ?: 'Not provided' }}</dd>
                    <dt class="col-sm-4">Reason</dt>
                    <dd class="col-sm-8">{{ $appointment->reason ?: 'Not provided' }}</dd>
                </dl>

                <div class="d-flex flex-wrap gap-2">
                    @foreach (['confirmed' => 'btn-success', 'declined' => 'btn-outline-danger', 'completed' => 'btn-primary', 'cancelled' => 'btn-outline-secondary'] as $next => $style)
                        @if ($appointment->canTransitionTo($next))
                            <form method="POST" action="{{ route('staff.appointments.status', $appointment) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $next }}">
                                <button class="btn {{ $style }}" type="submit">Mark as {{ $next }}</button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-3">Online consultation details</h2>
                @if ($appointment->status === 'confirmed')
                    <form method="POST" action="{{ route('staff.appointments.consultation', $appointment) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="meeting_link" class="form-label">Zoom meeting link (https)</label>
                            <input id="meeting_link" type="url" name="meeting_link" class="form-control @error('meeting_link') is-invalid @enderror" value="{{ old('meeting_link', $appointment->consultation->meeting_link ?? '') }}" required>
                            @error('meeting_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label for="meeting_id" class="form-label">Meeting ID</label>
                                <input id="meeting_id" name="meeting_id" class="form-control" value="{{ old('meeting_id', $appointment->consultation->meeting_id ?? '') }}">
                            </div>
                            <div class="col">
                                <label for="passcode" class="form-label">Passcode</label>
                                <input id="passcode" name="passcode" class="form-control" value="{{ old('passcode', $appointment->consultation->passcode ?? '') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions for the patient</label>
                            <textarea id="instructions" name="instructions" rows="3" class="form-control">{{ old('instructions', $appointment->consultation->instructions ?? '') }}</textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Save consultation details</button>
                    </form>
                @elseif ($appointment->consultation)
                    <p class="mb-0">A meeting link is saved for this appointment. It will be shown to the patient once the appointment is confirmed.</p>
                @else
                    <p class="text-secondary mb-0">Confirm the appointment to add the consultation details.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
