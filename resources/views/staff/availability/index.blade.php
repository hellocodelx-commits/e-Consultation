@extends('layouts.app')

@section('title', 'Availability')

@section('content')
<h1 class="h3 fw-bold mb-4">Availability</h1>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-3">Add a time slot</h2>
                <form method="POST" action="{{ route('staff.availability.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input id="date" type="date" name="date" value="{{ old('date') }}" class="form-control @error('date') is-invalid @enderror" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label for="start_time" class="form-label">Start</label>
                            <input id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" class="form-control @error('start_time') is-invalid @enderror" required>
                        </div>
                        <div class="col">
                            <label for="end_time" class="form-label">End</label>
                            <input id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" class="form-control @error('end_time') is-invalid @enderror" required>
                            @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Add slot</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Status</th><th scope="col"></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($slots as $slot)
                            <tr>
                                <td>{{ $slot->date->format('d M Y') }}</td>
                                <td>{{ $slot->time_range }}</td>
                                <td><x-status-badge :status="$slot->status" /></td>
                                <td class="text-end">
                                    @if ($slot->status === 'open')
                                        <form method="POST" action="{{ route('staff.availability.destroy', $slot) }}" onsubmit="return confirm('Remove this time slot?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-secondary p-4">No upcoming time slots. Add one using the form.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
