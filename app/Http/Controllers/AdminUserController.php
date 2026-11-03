<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $role = $request->query('role');

        $users = User::query()
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->when(in_array($role, User::ROLES, true), fn ($q) => $q->where('role', $role))
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
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', Rule::in([User::ROLE_STAFF, User::ROLE_ADMIN])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'specialization' => ['required_if:role,'.User::ROLE_STAFF, 'nullable', 'string', 'max:120'],
            'biography' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'],
                'password' => $data['password'],
            ]);

            if ($user->role === User::ROLE_STAFF) {
                StaffProfile::create([
                    'user_id' => $user->id,
                    'specialization' => $data['specialization'],
                    'biography' => $data['biography'] ?? null,
                ]);
            }

            return $user;
        });

        ActivityLog::record(auth()->user(), "Created a {$user->role} account for {$user->name}.");

        return redirect()->route('admin.users.index')->with('status', 'Account created.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate(['role' => ['required', Rule::in(User::ROLES)]]);

        $leavesStaff = $user->role === User::ROLE_STAFF && $data['role'] !== User::ROLE_STAFF;
        $hasOpenAppointments = $user->staffAppointments()
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->exists();

        if ($leavesStaff && $hasOpenAppointments) {
            return back()->with('error', 'This professional has pending or confirmed appointments. Resolve them before changing the role.');
        }

        $user->update(['role' => $data['role']]);

        ActivityLog::record(auth()->user(), "Changed {$user->name}'s role to {$data['role']}.");

        return back()->with('status', 'Role updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->patientAppointments()->exists() || $user->staffAppointments()->exists()) {
            return back()->with('error', 'This account has appointment records and cannot be deleted. You can change its role instead.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::record(auth()->user(), "Deleted the account for {$name}.");

        return redirect()->route('admin.users.index')->with('status', 'Account deleted.');
    }
}
