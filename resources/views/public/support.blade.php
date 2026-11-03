@extends('layouts.app')

@section('title', 'Support')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 fw-bold mb-3">Support</h1>
                <p class="text-secondary">If you have trouble booking or joining a consultation, contact the support team.</p>

                @if ($whatsappNumber)
                    <a class="btn btn-success" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $whatsappNumber) }}">Chat on WhatsApp</a>
                @else
                    <div class="alert alert-light border mb-0">WhatsApp support has not been set up yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
