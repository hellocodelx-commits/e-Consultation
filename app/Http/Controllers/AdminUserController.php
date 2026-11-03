<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');

        $users = User::query()
            ->when($search !== '', fn ($query) => $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when(in_array($role, User::ROLES, true), fn ($query) => $query->where('role', $role))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'role'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_STAFF, User::ROLE_ADMIN])],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        ActivityLog::record($request->user(), "Created {$user->role} account for {$user->name}.");

        return redirect()->route('admin.users.index')->with('status', 'Account created.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(User::ROLES)],
        ]);

        $user->update(['role' => $data['role']]);

        ActivityLog::record($request->user(), "Changed {$user->name}'s role to {$user->role}.");

        return redirect()->route('admin.users.index')->with('status', 'Role updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $hasAppointments = Appointment::query()
            ->where('patient_id', $user->id)
            ->orWhere('staff_id', $user->id)
            ->exists();

        if ($hasAppointments) {
            return back()->with('error', 'This account has appointments and cannot be deleted.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record($request->user(), "Deleted account for {$name}.");

        return redirect()->route('admin.users.index')->with('status', 'Account deleted.');
    }
}
