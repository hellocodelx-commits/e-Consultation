@extends('layouts.app')

@section('title', $staff->name)

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-1">{{ $staff->name }}</h1>
                <p class="text-primary fw-medium mb-3">{{ $staff->staffProfile->specialization }}</p>
                <p class="text-secondary">{{ $staff->staffProfile->biography ?: 'No biography has been added yet.' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-3">Available times</h2>

                @forelse ($slotsByDate as $date => $slots)
                    <div class="mb-4">
                        <h3 class="h6 text-secondary mb-2">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h3>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($slots as $slot)
                                @auth
                                    @if (auth()->user()->role === 'patient')
                                        <form method="POST" action="{{ route('patient.appointments.store', $slot) }}" onsubmit="return confirm('Request this time slot?');">
                                            @csrf
                                            <input type="hidden" name="reason" value="">
                                            <button class="btn btn-outline-primary btn-sm" type="submit">{{ $slot->time_range }}</button>
                                        </form>
                                    @else
                                        <span class="btn btn-outline-secondary btn-sm disabled">{{ $slot->time_range }}</span>
                                    @endif
                                @else
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">{{ $slot->time_range }}</a>
                                @endauth
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">There are no open times at the moment. Please check back later.</p>
                @endforelse

                @guest
                    <p class="small text-secondary mt-3 mb-0">Log in as a patient to request a time.</p>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection
