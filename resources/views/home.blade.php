@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="row align-items-center g-4 py-lg-4">
    <div class="col-lg-6">
        <h1 class="display-6 fw-bold mb-3">Book scheduled online consultations with healthcare professionals</h1>
        <p class="lead text-secondary mb-4">Create an account, request an available time slot, and receive your consultation details once your appointment is confirmed.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary btn-lg" href="{{ route('register') }}">Get started</a>
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('login') }}">Log in</a>
        </div>
        <p class="small text-secondary mt-4 mb-0">This system is for scheduled consultations only. It is not for emergencies.</p>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold">How it works</h2>
                <ol class="mb-0 ps-3">
                    <li class="mb-2">Create a patient account.</li>
                    <li class="mb-2">Choose an available time with a professional.</li>
                    <li class="mb-2">Wait for the appointment to be confirmed.</li>
                    <li>Join the online consultation at the scheduled time.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
