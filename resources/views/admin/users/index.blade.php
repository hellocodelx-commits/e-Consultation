@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <h1 class="h3 fw-bold mb-0">Users</h1>
    <div class="d-flex flex-column flex-sm-row gap-2">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex flex-column flex-sm-row gap-2">
            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name or email" aria-label="Search users">
            <select name="role" class="form-select" aria-label="Filter by role">
                <option value="">All roles</option>
                @foreach (\App\Models\User::ROLES as $option)
                    <option value="{{ $option }}" @selected($role === $option)>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary" type="submit">Filter</button>
        </form>
        <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Add staff or admin</a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr><th scope="col">Name</th><th scope="col">Email</th><th scope="col">Role</th><th scope="col">Change role</th><th scope="col"></th></tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-capitalize">{{ $user->role }}</td>
                        <td>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.role', $user) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm" aria-label="New role">
                                        @foreach (\App\Models\User::ROLES as $option)
                                            <option value="{{ $option }}" @selected($user->role === $option)>{{ ucfirst($option) }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
                                </form>
                            @else
                                <span class="text-secondary small">You</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
