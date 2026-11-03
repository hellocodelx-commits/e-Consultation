@extends('layouts.app')

@section('title', 'Professionals')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Find a professional</h1>
        <p class="text-secondary mb-0">Browse healthcare professionals and book an available time.</p>
    </div>
    <form method="GET" action="{{ route('professionals.index') }}" class="d-flex gap-2">
        <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name or specialisation" aria-label="Search professionals">
        <button class="btn btn-primary" type="submit">Search</button>
    </form>
</div>

@if ($professionals->isEmpty())
    <div class="alert alert-light border">No professionals match your search yet.</div>
@endif

<div class="row g-4">
    @foreach ($professionals as $professional)
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body p-4 d-flex flex-column">
                    <h2 class="h5 fw-semibold mb-1">{{ $professional->name }}</h2>
                    <p class="text-primary small fw-medium mb-3">{{ $professional->staffProfile->specialization }}</p>
                    <p class="text-secondary small flex-grow-1">{{ \Illuminate\Support\Str::limit($professional->staffProfile->biography, 120) }}</p>
                    <a class="btn btn-outline-primary mt-2" href="{{ route('professionals.show', $professional) }}">View availability</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4">{{ $professionals->links() }}</div>
@endsection
