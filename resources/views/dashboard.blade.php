@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h1 class="h4 fw-bold mb-2">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-secondary mb-0">Your appointment booking tools will appear here.</p>
    </div>
</div>
@endsection
