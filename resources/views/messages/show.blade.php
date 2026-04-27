@extends('layouts.app')
 
@section('title', 'Chat with ' . $tenant->user->name)
@section('page-title', 'Messages')
 
@section('content')
 
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary"
       style="border-radius:8px">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="d-flex align-items-center gap-2">
        <div style="width:38px;height:38px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#1a7a4a">
            {{ strtoupper(substr($tenant->user->name, 0, 2)) }}
        </div>
        <div>
            <div style="font-size:.95rem;font-weight:700;color:#1a1a2e">{{ $tenant->user->name }}</div>
            <div style="font-size:.75rem;color:#6c757d">{{ $tenant->user->phone }}</div>
        </div>
    </div>
</div>
 
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
 
        {{-- Messages --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
            <div class="card-body p-4" id="message-container"
                 style="height:420px;overflow-y:auto;display:flex;flex-direction:column;gap:12px">
 
                @if($messages->isEmpty())
                    <div class="text-center text-muted py-5" style="font-size:.82rem">
                        <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                        No messages yet. Start the conversation.
                    </div>
                @else
                    @foreach($messages as $message)
                    @php $isMe = $message->sender_id === auth()->id(); @endphp
                    <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                        <div style="max-width:75%">
                            @if(!$isMe)
                            <div style="font-size:.7rem;color:#6c757d;margin-bottom:3px;padding-left:4px">
                                {{ $message->sender->name }}
                            </div>
                            @endif
                            <div style="background:{{ $isMe ? '#1a7a4a' : '#f0fdf4' }};
                                        color:{{ $isMe ? '#fff' : '#1a1a2e' }};
                                        padding:10px 14px;
                                        border-radius:{{ $isMe ? '12px 12px 2px 12px' : '12px 12px 12px 2px' }};
                                        font-size:.83rem;
                                        line-height:1.5">
                                @if($message->body)
                                    {{ $message->body }}
                                @endif
                                @if($message->file_path)
                                    @php
                                        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                        $isImage = in_array($message->file_type, $imageTypes);
                                    @endphp
                                    @if($isImage)
                                        <div style="margin-top:{{ $message->body ? '8px' : '0' }}">
                                            <img src="{{ asset('storage/' . $message->file_path) }}"
                                                 alt="{{ $message->file_name }}"
                                                 style="max-width:220px;border-radius:8px;display:block;cursor:pointer"
                                                 onclick="window.open('{{ asset('storage/' . $message->file_path) }}', '_blank')">
                                        </div>
                                    @else
                                        <div style="margin-top:{{ $message->body ? '8px' : '0' }}">
                                            <a href="{{ asset('storage/' . $message->file_path) }}"
                                               target="_blank"
                                               style="color:{{ $isMe ? '#fff' : '#1a7a4a' }};font-size:.78rem;display:flex;align-items:center;gap:6px;text-decoration:none">
                                                <i class="bi bi-file-earmark"></i>
                                                {{ $message->file_name }}
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div style="font-size:.68rem;color:#9ca3af;margin-top:3px;text-align:{{ $isMe ? 'right' : 'left' }};padding:0 4px">
                                {{ $message->created_at->format('d M, h:i A') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
 
        {{-- Reply Form --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body p-3">
                <form action="{{ route('messages.reply', $tenant) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex gap-2 align-items-end">
                        <div style="flex:1">
                            <input type="text" name="body" id="reply-input" class="form-control mb-2"
                                   placeholder="Type a reply..."
                                   style="border-radius:8px;font-size:.85rem;border:1.5px solid #e5e7eb"
                                   autocomplete="off">
                            <div id="file-preview" style="display:none;font-size:.75rem;color:#6c757d;margin-bottom:6px">
                                <i class="bi bi-paperclip me-1"></i>
                                <span id="file-name"></span>
                                <button type="button" onclick="clearFile()"
                                        style="background:none;border:none;color:#e74c3c;cursor:pointer;padding:0 4px">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                            <input type="file" name="file" id="file-input"
                                   accept="image/*,.pdf,.doc,.docx"
                                   style="display:none"
                                   onchange="showFilePreview(this)">
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" onclick="document.getElementById('file-input').click()"
                                    class="btn btn-sm btn-outline-secondary"
                                    style="border-radius:8px;padding:8px 12px"
                                    title="Attach file">
                                <i class="bi bi-paperclip"></i>
                            </button>
                            <button type="submit" class="btn btn-sm"
                                    style="background:#1a7a4a;color:#fff;border-radius:8px;padding:8px 16px;font-size:.85rem;white-space:nowrap">
                                <i class="bi bi-send me-1"></i> Send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
 
    </div>
</div>
 
@push('scripts')
<script>
    const container = document.getElementById('message-container');
    if (container) container.scrollTop = container.scrollHeight;
 
    function showFilePreview(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name').textContent = input.files[0].name;
            document.getElementById('file-preview').style.display = 'block';
        }
    }
 
    function clearFile() {
        document.getElementById('file-input').value = '';
        document.getElementById('file-preview').style.display = 'none';
        document.getElementById('file-name').textContent = '';
    }
</script>
@endpush
 
@endsection
 