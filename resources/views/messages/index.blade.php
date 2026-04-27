@extends('layouts.app')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-1" style="font-size:1.15rem;font-weight:700;color:#1a1a2e">Tenant Messages</h2>
        <p class="text-muted mb-0" style="font-size:.82rem">All tenant conversations</p>
    </div>
</div>

@if($conversations->isEmpty())
    <div class="text-center py-5">
        <div style="font-size:3rem;color:#d1d5db"><i class="bi bi-chat-dots"></i></div>
        <h3 style="font-size:1rem;font-weight:700;color:#1a1a2e;margin-top:12px">No messages yet</h3>
        <p class="text-muted" style="font-size:.82rem">Tenant messages will appear here</p>
    </div>
@else
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        @foreach($conversations as $conversation)
        <a href="{{ route('messages.show', $conversation) }}"
           style="text-decoration:none;color:inherit">
            <div class="d-flex align-items-center gap-3 p-3"
                 style="border-bottom:1px solid #f0f0f0;transition:background .15s"
                 onmouseover="this.style.background='#f8fafc'"
                 onmouseout="this.style.background='transparent'">

                <div style="width:42px;height:42px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:700;color:#1a7a4a;flex-shrink:0">
                    {{ strtoupper(substr($conversation->user->name, 0, 2)) }}
                </div>

                <div style="flex:1;min-width:0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div style="font-size:.88rem;font-weight:{{ $conversation->unread_count > 0 ? '700' : '600' }};color:#1a1a2e">
                            {{ $conversation->user->name }}
                        </div>
                        <div style="font-size:.72rem;color:#9ca3af">
                            {{ $conversation->last_message?->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <div style="font-size:.78rem;color:#6c757d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px">
                            @if($conversation->last_message?->sender_id === auth()->id())
                                <span style="color:#1a7a4a">You: </span>
                            @endif
                            {{ $conversation->last_message?->body }}
                        </div>
                        @if($conversation->unread_count > 0)
                            <span style="background:#1a7a4a;color:#fff;border-radius:20px;padding:2px 8px;font-size:.68rem;font-weight:700;flex-shrink:0">
                                {{ $conversation->unread_count }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
@endif

@endsection