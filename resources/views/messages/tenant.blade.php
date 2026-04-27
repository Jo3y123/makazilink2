<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f8;
            margin: 0;
        }
        .portal-header {
            background: #0f2d1e;
            color: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .portal-header .brand {
            font-weight: 700;
            font-size: .95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .portal-header .brand-icon {
            width: 30px;
            height: 30px;
            background: #1a7a4a;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
        }
        .portal-content {
            max-width: 700px;
            margin: 32px auto;
            padding: 0 20px;
        }
    </style>
</head>
<body>
 
<header class="portal-header">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-buildings text-white"></i></div>
        {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }} Tenant Portal
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('tenant.portal') }}"
           style="font-size:.82rem;color:rgba(255,255,255,.6);text-decoration:none">
            <i class="bi bi-arrow-left me-1"></i>Back to Portal
        </a>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit"
                    style="background:none;border:none;color:rgba(255,255,255,.5);font-size:.8rem;cursor:pointer;padding:0">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>
</header>
 
<div class="portal-content">
 
    <div class="mb-4">
        <h2 style="font-size:1.1rem;font-weight:700;color:#1a1a2e;margin-bottom:4px">
            <i class="bi bi-chat-dots me-2" style="color:#1a7a4a"></i>Messages
        </h2>
        <p class="text-muted mb-0" style="font-size:.82rem">
            Send a message to your landlord or caretaker
        </p>
    </div>
 
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" style="border-radius:8px;font-size:.82rem">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" style="border-radius:8px;font-size:.82rem">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    {{-- Messages --}}
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
        <div class="card-body p-4" id="message-container"
             style="height:380px;overflow-y:auto;display:flex;flex-direction:column;gap:12px">
 
            @if($messages->isEmpty())
                <div class="text-center text-muted py-5" style="font-size:.82rem">
                    <i class="bi bi-chat-dots fs-3 d-block mb-2"></i>
                    No messages yet. Send your first message below.
                </div>
            @else
                @foreach($messages as $message)
                @php $isMe = $message->sender_id === auth()->id(); @endphp
                <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                    <div style="max-width:78%">
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
                                             style="max-width:200px;border-radius:8px;display:block;cursor:pointer"
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
 
    {{-- Send Message Form --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px">
        <div class="card-body p-3">
            <form action="{{ route('tenant.messages.send') }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="d-flex gap-2 align-items-end">
                    <div style="flex:1">
                        <input type="text" name="body" class="form-control mb-2"
                               placeholder="Type your message..."
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
                                title="Attach photo or file">
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
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
 