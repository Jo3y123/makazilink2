@extends('layouts.app')

@section('title', $page ?? 'Coming Soon')
@section('page-title', $page ?? 'Coming Soon')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 60vh">
    <div class="text-center">
        <div class="mb-4" style="font-size: 3.5rem; color: #d1d5db;">
            <i class="bi bi-hammer"></i>
        </div>
        <h2 style="font-size: 1.2rem; font-weight: 700; color: #1a1a2e;">
            {{ $page ?? 'This Page' }} — Coming Soon
        </h2>
        <p class="text-muted mb-4" style="font-size: .85rem; max-width: 340px; margin: 0 auto;">
            This section is being built. It will be ready in the next development day.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-sm"
           style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 20px;font-size:.85rem;font-weight:600;">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>
@endsection