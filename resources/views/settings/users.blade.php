@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Users</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">Manage system users and roles</p>
    </div>
    <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal"
            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
        <i class="bi bi-plus-lg me-1"></i> Add User
    </button>
</div>

<div class="card border-0 shadow-sm" style="border-radius:12px">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d">
                <tr>
                    <th class="px-4 py-3">User</th>
                    <th class="py-3">Phone</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Status</th>
                    <th class="py-3">Created</th>
                    <th class="py-3">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size:.85rem">
                @foreach($users as $user)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:34px;height:34px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a7a4a;flex-shrink:0">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#1a1a2e">{{ $user->name }}</div>
                                <div style="font-size:.75rem;color:#6c757d">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">{{ $user->phone ?? '—' }}</td>
                    <td class="py-3">
                        <span class="badge role-{{ $user->role }}"
                              style="border-radius:20px;font-size:.7rem;padding:4px 10px">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="py-3">
                        @if($user->is_active)
                            <span class="badge" style="background:#dcfce7;color:#15803d;border-radius:20px;font-size:.7rem">Active</span>
                        @else
                            <span class="badge" style="background:#fee2e2;color:#b91c1c;border-radius:20px;font-size:.7rem">Inactive</span>
                        @endif
                    </td>
                    <td class="py-3">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="py-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('settings.users.edit', $user) }}"
                               class="btn btn-sm btn-outline-secondary"
                               style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('settings.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        style="font-size:.75rem;border-radius:6px;padding:4px 12px">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #f0f0f0">
                <h5 class="modal-title" style="font-size:.95rem;font-weight:700">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               placeholder="e.g. John Kamau" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" class="form-control"
                               placeholder="john@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Phone
                        </label>
                        <input type="text" name="phone" class="form-control"
                               placeholder="0712345678">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="agent">Agent</option>
                            <option value="accountant">Accountant</option>
                            <option value="caretaker">Caretaker</option>
                            <option value="tenant">Tenant</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Minimum 6 characters" required>
                    </div>

                </div>
                <div class="modal-footer" style="border-top:1px solid #f0f0f0">
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:6px 20px;font-size:.85rem;font-weight:600;">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection