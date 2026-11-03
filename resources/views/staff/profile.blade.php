@extends('layouts.app')

@section('title', 'My profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-4">My profile</h1>
                <form method="POST" action="{{ route('staff.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Full name</label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label for="specialization" class="form-label">Specialisation</label>
                        <input id="specialization" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $profile->specialization ?? '') }}" required>
                        @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="biography" class="form-label">Biography</label>
                        <textarea id="biography" name="biography" rows="5" class="form-control">{{ old('biography', $profile->biography ?? '') }}</textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Save profile</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
