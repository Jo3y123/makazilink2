@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('settings.users') }}" class="btn btn-sm btn-outline-secondary"
               style="border-radius:8px">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h2 class="mb-0" style="font-size:1.1rem;font-weight:700;color:#1a1a2e">Edit User</h2>
                <p class="text-muted mb-0" style="font-size:.8rem">{{ $user->name }}</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-4">
                <form action="{{ route('settings.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Email
                        </label>
                        <input type="email" class="form-control"
                               value="{{ $user->email }}" disabled>
                        <small class="text-muted" style="font-size:.72rem">Email cannot be changed</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Phone
                        </label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="0712345678">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" class="form-select" required>
                            <option value="admin"       {{ $user->role === 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="agent"       {{ $user->role === 'agent'       ? 'selected' : '' }}>Agent</option>
                            <option value="accountant"  {{ $user->role === 'accountant'  ? 'selected' : '' }}>Accountant</option>
                            <option value="caretaker"   {{ $user->role === 'caretaker'   ? 'selected' : '' }}>Caretaker</option>
                            <option value="tenant"      {{ $user->role === 'tenant'      ? 'selected' : '' }}>Tenant</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            Status
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="is_active" id="is_active" value="1"
                                   {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active"
                                   style="font-size:.82rem">Account is active</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="font-size:.8rem;font-weight:600;color:#374151">
                            New Password
                        </label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Leave blank to keep current password">
                        <small class="text-muted" style="font-size:.72rem">
                            Only fill this if you want to change the password
                        </small>
                    </div>

                    <button type="submit" class="btn w-100"
                            style="background:#1a7a4a;color:#fff;border-radius:8px;padding:11px;font-size:.9rem;font-weight:600;">
                        <i class="bi bi-check-lg me-2"></i>Save Changes
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection