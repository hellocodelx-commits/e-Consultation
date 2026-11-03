@extends('layouts.app')

@section('title', 'Activity')

@section('content')
<h1 class="h3 fw-bold mb-4">Activity log</h1>

<div class="card border-0 shadow-sm">
    <ul class="list-group list-group-flush">
        @forelse ($logs as $log)
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between gap-1">
                <span>{{ $log->description }}</span>
                <span class="text-secondary small">{{ $log->user?->name ?? 'Deleted user' }} &middot; {{ $log->created_at->format('d M Y, H:i') }}</span>
            </li>
        @empty
            <li class="list-group-item text-secondary">No activity recorded yet.</li>
        @endforelse
    </ul>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
