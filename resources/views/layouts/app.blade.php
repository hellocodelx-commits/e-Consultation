<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'e-Consultation') | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .btn-primary { --bs-btn-bg: #0f766e; --bs-btn-border-color: #0f766e; --bs-btn-hover-bg: #0d625c; --bs-btn-hover-border-color: #0d625c; --bs-btn-active-bg: #0d625c; --bs-btn-active-border-color: #0d625c; --bs-btn-disabled-bg: #0f766e; --bs-btn-disabled-border-color: #0f766e; }
        .btn { transition: transform .15s ease, box-shadow .15s ease; }
        .btn:hover { transform: translateY(-1px); }
        main { animation: fadeIn .4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
<nav class="navbar navbar-expand-md bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-semibold text-dark" href="{{ route('home') }}">e-Consultation</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <div class="ms-auto d-flex flex-column flex-md-row gap-2 align-items-md-center py-2 py-md-0">
                @auth
                    <span class="text-secondary small">Signed in as {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Log out</button>
                    </form>
                @else
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Log in</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Create account</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<main class="container py-4 py-lg-5 flex-grow-1">
    @if (session('status'))
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<footer class="border-top bg-white py-3 small text-secondary">
    <div class="container">e-Consultation Management System</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
