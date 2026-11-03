@props(['status'])
@php
    $map = [
        'open' => ['success', 'Open'],
        'booked' => ['secondary', 'Booked'],
        'pending' => ['warning', 'Pending'],
        'confirmed' => ['success', 'Confirmed'],
        'declined' => ['danger', 'Declined'],
        'cancelled' => ['secondary', 'Cancelled'],
        'completed' => ['primary', 'Completed'],
    ];
    [$color, $label] = $map[$status] ?? ['secondary', ucfirst($status)];
@endphp
<span class="badge rounded-pill text-bg-{{ $color }}">{{ $label }}</span>
