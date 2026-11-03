@extends('layouts.app')

@section('title', 'Admin dashboard')

@section('content')
<h1 class="h3 fw-bold mb-4">Administration</h1>

<div class="row g-3 mb-4">
    @foreach (['patient' => 'Patients', 'staff' => 'Professionals', 'admin' => 'Administrators'] as $role => $label)
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <p class="text-secondary small mb-1">{{ $label }}</p>
                <p class="h2 fw-bold mb-0">{{ $roleCounts[$role] ?? 0 }}</p>
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <h2 class="h5 fw-semibold mb-3">Appointments by status</h2>
            <ul class="list-group list-group-flush">
                @forelse ($statusCounts as $status => $total)
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-capitalize">{{ $status }}</span><span class="fw-semibold">{{ $total }}</span></li>
                @empty
                    <li class="list-group-item px-0 text-secondary">No appointments yet.</li>
                @endforelse
            </ul>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-semibold mb-0">Recent activity</h2>
                <a class="small" href="{{ route('admin.activity.index') }}">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($recentActivity as $log)
                    <li class="list-group-item px-0 small">{{ $log->description }} <span class="text-secondary">({{ $log->user?->name ?? 'Deleted user' }}, {{ $log->created_at->diffForHumans() }})</span></li>
                @empty
                    <li class="list-group-item px-0 text-secondary">No activity recorded yet.</li>
                @endforelse
            </ul>
        </div></div>
    </div>
</div>
@endsection
