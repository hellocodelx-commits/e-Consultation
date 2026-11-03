@extends('layouts.app')

@section('title', 'Staff dashboard')

@section('content')
<h1 class="h3 fw-bold mb-4">Dashboard</h1>

@unless ($hasProfile)
    <div class="alert alert-warning">Your profile is not visible to patients yet. <a href="{{ route('staff.profile.edit') }}">Add your specialisation</a>.</div>
@endunless

<div class="row g-3 mb-4">
    <div class="col-sm-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary small mb-1">Pending requests</p><p class="h2 fw-bold mb-0">{{ $counts['pending'] }}</p></div></div></div>
    <div class="col-sm-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary small mb-1">Confirmed appointments</p><p class="h2 fw-bold mb-0">{{ $counts['confirmed'] }}</p></div></div></div>
    <div class="col-sm-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary small mb-1">Open time slots</p><p class="h2 fw-bold mb-0">{{ $counts['openSlots'] }}</p></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-semibold mb-0">Upcoming requests</h2>
            <a class="small" href="{{ route('staff.appointments.index') }}">View all</a>
        </div>
        <ul class="list-group list-group-flush">
            @forelse ($upcoming as $appointment)
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <a href="{{ route('staff.appointments.show', $appointment) }}">{{ $appointment->patient->name }}, {{ $appointment->appointment_date->format('d M Y') }} {{ $appointment->time_range }}</a>
                    <x-status-badge :status="$appointment->status" />
                </li>
            @empty
                <li class="list-group-item px-0 text-secondary">No upcoming requests.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
