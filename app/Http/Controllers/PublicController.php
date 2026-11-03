<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function professionals(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $professionals = User::query()
            ->where('role', User::ROLE_STAFF)
            ->whereHas('staffProfile')
            ->with('staffProfile')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('staffProfile', fn ($p) => $p->where('specialization', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('public.professionals.index', compact('professionals', 'search'));
    }

    public function showProfessional(User $staff): View
    {
        abort_unless($staff->role === User::ROLE_STAFF && $staff->staffProfile()->exists(), 404);

        $staff->load('staffProfile');

        $slotsByDate = $staff->availabilitySlots()
            ->open()
            ->upcoming()
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn ($slot) => $slot->date->format('Y-m-d'));

        return view('public.professionals.show', compact('staff', 'slotsByDate'));
    }

    public function support(): View
    {
        return view('public.support', ['whatsappNumber' => config('services.whatsapp.number')]);
    }
}
