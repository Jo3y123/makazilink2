<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MakaziLink v2') — Rental Management</title>
 
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
 
    <style>
        :root {
            --ml-green:      #1a7a4a;
            --ml-green-dark: #155c38;
            --ml-green-light:#e8f5ee;
            --ml-sidebar-w:  260px;
            --ml-topbar-h:   60px;
        }
 
        * { box-sizing: border-box; }
 
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f6f8;
            margin: 0;
        }
 
        #sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--ml-sidebar-w);
            height: 100vh;
            background: #0f2d1e;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            z-index: 1000;
        }
 
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
 
        .sidebar-brand .brand-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
 
        .sidebar-brand .brand-logo .logo-icon {
            width: 34px;
            height: 34px;
            background: var(--ml-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0;
        }
 
        .sidebar-brand .brand-sub {
            font-size: .7rem;
            color: rgba(255,255,255,.4);
            margin-top: 2px;
            padding-left: 44px;
        }
 
        .nav-section-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.3);
            padding: 16px 20px 6px;
            font-weight: 600;
        }
 
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: .85rem;
            font-weight: 500;
            transition: background .15s, color .15s;
        }
 
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.07);
            color: #fff;
        }
 
        .sidebar-nav .nav-link.active {
            border-left: 3px solid var(--ml-green);
            padding-left: 17px;
        }
 
        .sidebar-nav .nav-link i {
            width: 18px;
            text-align: center;
            font-size: .95rem;
            opacity: .8;
        }
 
        .sidebar-footer {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
 
        .sidebar-footer .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
        }
 
        .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--ml-green);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .75rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
 
        .user-name {
            font-size: .82rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }
 
        .user-role {
            font-size: .68rem;
            color: rgba(255,255,255,.4);
            text-transform: capitalize;
        }
 
        .btn-logout {
            margin-left: auto;
            background: none;
            border: none;
            color: rgba(255,255,255,.4);
            padding: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
 
        .btn-logout:hover { color: #e74c3c; }
 
        #topbar {
            position: fixed;
            top: 0;
            left: var(--ml-sidebar-w);
            right: 0;
            height: var(--ml-topbar-h);
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 999;
        }
 
        .page-title {
            font-size: 1rem;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }
 
        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }
 
        .role-badge {
            font-size: .7rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
 
        .role-admin      { background: #ffecd2; color: #b45309; }
        .role-agent      { background: #dbeafe; color: #1e40af; }
        .role-accountant { background: #dcfce7; color: #15803d; }
        .role-caretaker  { background: #f3e8ff; color: #7e22ce; }
        .role-tenant     { background: #fee2e2; color: #b91c1c; }
 
        #main-content {
            margin-left: var(--ml-sidebar-w);
            margin-top: var(--ml-topbar-h);
            padding: 28px;
            min-height: calc(100vh - var(--ml-topbar-h));
            width: calc(100% - var(--ml-sidebar-w));
            overflow-x: hidden;
        }
 
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px 22px;
            border: 1px solid #e9ecef;
        }
 
        .stat-card .stat-label {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            margin-bottom: 6px;
        }
 
        .stat-card .stat-value {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }
 
        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
 
        .alert { border-radius: 8px; font-size: .875rem; }
 
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; width: 100%; }
        }
 
        /* Chatbot */
        #chatbot-btn {
            position: fixed;
            bottom: 28px;
            right: 28px;
            width: 52px;
            height: 52px;
            background: #1a7a4a;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            box-shadow: 0 4px 16px rgba(26,122,74,.4);
            z-index: 9999;
            transition: transform .2s;
            flex-shrink: 0;
        }
 
        #chatbot-btn:hover { transform: scale(1.08); }
 
        #chatbot-box {
            position: fixed;
            bottom: 92px;
            right: 28px;
            width: 360px;
            height: 520px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,.15);
            z-index: 9998;
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
 
        #chatbot-box.open { display: flex; }
 
        .chatbot-header {
            background: #0f2d1e;
            color: #fff;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
 
        .chatbot-header .title {
            font-size: .88rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
 
        .chatbot-header .close-btn {
            background: none;
            border: none;
            color: rgba(255,255,255,.6);
            cursor: pointer;
            font-size: 1rem;
            padding: 0;
        }
 
        .chatbot-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
 
        .msg {
            max-width: 90%;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: .82rem;
            line-height: 1.5;
            white-space: pre-wrap;
        }
 
        .msg.bot {
            background: #f0fdf4;
            color: #1a1a2e;
            border-bottom-left-radius: 2px;
            align-self: flex-start;
        }
 
        .msg.user {
            background: #1a7a4a;
            color: #fff;
            border-bottom-right-radius: 2px;
            align-self: flex-end;
        }
 
        .msg.typing {
            background: #f0fdf4;
            color: #6c757d;
            align-self: flex-start;
            font-style: italic;
        }
 
        .chat-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-self: flex-start;
            max-width: 100%;
        }
 
        .chip {
            background: #fff;
            border: 1.5px solid #1a7a4a;
            color: #1a7a4a;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, color .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
 
        .chip:hover {
            background: #1a7a4a;
            color: #fff;
        }
 
        .chip.secondary {
            border-color: #e9ecef;
            color: #6c757d;
        }
 
        .chip.secondary:hover {
            background: #f4f6f8;
            color: #1a1a2e;
        }
 
        .chatbot-input {
            padding: 12px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }
 
        .chatbot-input input {
            flex: 1;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: .82rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
        }
 
        .chatbot-input input:focus { border-color: #1a7a4a; }
 
        .chatbot-input button {
            background: #1a7a4a;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: .82rem;
            cursor: pointer;
        }
 
        .chatbot-input button:hover { background: #155c38; }
    </style>
 
    @stack('styles')
</head>
<body>
 
<nav id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <span class="logo-icon"><i class="bi bi-buildings text-white"></i></span>
            MakaziLink
        </a>
        <div class="brand-sub">Rental Management v2</div>
    </div>
 
    <div class="sidebar-nav mt-1">
        <div class="nav-section-label">Overview</div>
 
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
 
        @hasrole(['admin', 'agent'])
        <div class="nav-section-label">Properties</div>
        <a href="{{ route('properties.index') }}"
           class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Properties
        </a>
        @endhasrole
 
        @hasrole(['admin', 'agent', 'caretaker'])
        <a href="{{ route('units.index') }}"
           class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
            <i class="bi bi-door-open"></i> Units
        </a>
        <a href="{{ route('tenants.index') }}"
           class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Tenants
        </a>
        @endhasrole
 
        @hasrole(['admin', 'agent'])
        <a href="{{ route('leases.index') }}"
           class="nav-link {{ request()->routeIs('leases.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Leases
        </a>
        @endhasrole
 
        @hasrole(['admin', 'accountant'])
        <div class="nav-section-label">Finance</div>
        <a href="{{ route('payments.index') }}"
        class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i> Payments
        </a>
        <a href="{{ route('invoices.index') }}"
        class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Invoices
        </a>
        <a href="{{ route('reports.index') }}"
        class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Reports
        </a>
        <a href="{{ route('reports.properties') }}"
        class="nav-link {{ request()->routeIs('reports.properties') || request()->routeIs('reports.property.*') ? 'active' : '' }}">
            <i class="bi bi-building-check"></i> Property Reports
        </a>
        @endhasrole
 
        @hasrole(['admin', 'caretaker'])
        <div class="nav-section-label">Operations</div>
        <a href="{{ route('maintenance.index') }}"
           class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools"></i> Maintenance
        </a>
        @endhasrole
 
        @hasrole(['admin', 'caretaker', 'accountant'])
        <a href="{{ route('water.index') }}"
           class="nav-link {{ request()->routeIs('water.*') ? 'active' : '' }}">
            <i class="bi bi-droplet"></i> Water Readings
        </a>
        @endhasrole
 
        @hasrole(['admin'])
        <div class="nav-section-label">Administration</div>
        <a href="{{ route('settings.index') }}"
           class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Settings
        </a>
        <a href="{{ route('settings.users') }}"
           class="nav-link {{ request()->routeIs('settings.users') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i> Users
        </a>
        @endhasrole
    </div>
 
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
 
<header id="topbar">
    <button class="btn btn-sm d-md-none me-3"
            onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list fs-5"></i>
    </button>
    <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
    <div class="topbar-right">
        <span class="role-badge role-{{ auth()->user()->role }}">
            {{ auth()->user()->role }}
        </span>
    </div>
</header>
 
<main id="main-content">
 
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    @yield('content')
</main>
 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
 
{{-- Chatbot Widget --}}
<button id="chatbot-btn" onclick="toggleChatbot()" title="Ask MakaziLink Assistant">
    <i class="bi bi-robot"></i>
</button>
 
<div id="chatbot-box">
    <div class="chatbot-header">
        <div class="title">
            <i class="bi bi-robot"></i> MakaziLink Assistant
        </div>
        <button class="close-btn" onclick="toggleChatbot()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="msg bot">Hi {{ auth()->user()->name }}! I am your MakaziLink assistant.
 
Type a question or type <strong>help</strong> to browse topics.</div>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbot-input" placeholder="Ask me anything..."
               onkeypress="if(event.key==='Enter') sendMessage()">
        <button onclick="sendMessage()"><i class="bi bi-send"></i></button>
    </div>
</div>
 
<script>
const ROLE = '{{ auth()->user()->role }}';
let lastCategory = '';
 
const categories = {
    admin: [
        { label: '🏢 Properties', questions: ['Total properties', 'Property types'] },
        { label: '🚪 Units', questions: ['Vacant units', 'Occupied units', 'Total units', 'Occupancy rate', 'Units under maintenance'] },
        { label: '👥 Tenants', questions: ['Total tenants', 'New tenants this month', 'Tenants without lease'] },
        { label: '📄 Leases', questions: ['Active leases', 'Expiring leases', 'Terminated leases', 'Open ended leases'] },
        { label: '💰 Finance', questions: ['This month revenue', 'Last month revenue', 'Total revenue', 'Today payments', 'Unpaid invoices', 'Overdue invoices', 'Outstanding balance', 'Mpesa payments', 'Cash payments'] },
        { label: '🔧 Maintenance', questions: ['Maintenance summary', 'Open maintenance', 'Urgent maintenance', 'Plumbing issues', 'Electrical issues', 'Maintenance cost'] },
        { label: '💧 Water', questions: ['Water readings this month', 'Total water readings', 'Units with water meter'] },
        { label: '📊 Summaries', questions: ['Daily summary', 'Weekly summary', 'Monthly summary'] },
        { label: '📖 How-to Guides', questions: ['How do I add a tenant', 'How do I create an invoice', 'How do I record a payment', 'How do I add a property', 'How do I add a unit', 'How do I create a lease', 'How do I send a receipt via WhatsApp', 'How do I download a PDF', 'How do I deactivate a user'] },
    ],
    agent: [
        { label: '🏢 Properties', questions: ['Total properties', 'Property types'] },
        { label: '🚪 Units', questions: ['Vacant units', 'Occupied units', 'Total units', 'Occupancy rate'] },
        { label: '👥 Tenants', questions: ['Total tenants', 'New tenants this month', 'Tenants without lease'] },
        { label: '📄 Leases', questions: ['Active leases', 'Expiring leases', 'Terminated leases'] },
        { label: '📖 How-to Guides', questions: ['How do I add a tenant', 'How do I create a lease', 'How do I add a property', 'How do I add a unit'] },
    ],
    accountant: [
        { label: '💰 Revenue', questions: ['This month revenue', 'Last month revenue', 'Total revenue', 'Today payments', 'This week payments'] },
        { label: '💳 Payments', questions: ['Total payments', 'Mpesa payments', 'Cash payments', 'Bank payments'] },
        { label: '🧾 Invoices', questions: ['Total invoices', 'Unpaid invoices', 'Overdue invoices', 'Paid invoices', 'Partial payments', 'Outstanding balance'] },
        { label: '📊 Summaries', questions: ['Monthly summary', 'Weekly summary'] },
        { label: '📖 How-to Guides', questions: ['How do I create an invoice', 'How do I record a payment', 'How do I download a PDF'] },
    ],
    caretaker: [
        { label: '🔧 Maintenance', questions: ['Maintenance summary', 'Open maintenance', 'Urgent maintenance', 'High priority maintenance', 'Plumbing issues', 'Electrical issues', 'Maintenance this month', 'Resolved maintenance', 'Maintenance cost'] },
        { label: '💧 Water', questions: ['Water readings this month', 'Total water readings', 'Units with water meter'] },
        { label: '🚪 Units', questions: ['Units under maintenance', 'Total units'] },
        { label: '📖 How-to Guides', questions: ['How do I log a maintenance request', 'How do I record a water reading'] },
    ],
    tenant: [
        { label: '💸 Rent', questions: ['My rent amount', 'When is rent due', 'My balance', 'Total I have paid', 'Have I paid this month', 'Am I up to date'] },
        { label: '📄 Lease', questions: ['My lease details', 'When does my lease expire', 'My notice period', 'My deposit'] },
        { label: '🏠 My Home', questions: ['My unit', 'My property address', 'Landlord contact'] },
        { label: '🧾 Payments', questions: ['My payment history', 'My pending invoices'] },
        { label: '🔧 Maintenance', questions: ['My maintenance requests'] },
    ],
};
 
function toggleChatbot() {
    const box = document.getElementById('chatbot-box');
    box.classList.toggle('open');
    if (box.classList.contains('open')) {
        document.getElementById('chatbot-input').focus();
    }
}
 
function showCategories() {
    const msgs = document.getElementById('chatbot-messages');
    const cats = categories[ROLE] || [];
    if (cats.length === 0) return;
    msgs.innerHTML += `<div class="msg bot">Here are the topics I can help you with:</div>`;
    const chips = cats.map(c =>
        `<button class="chip" onclick="showQuestions('${c.label}')">${c.label}</button>`
    ).join('');
    msgs.innerHTML += `<div class="chat-chips">${chips}</div>`;
    msgs.scrollTop = msgs.scrollHeight;
}
 
function showQuestions(categoryLabel) {
    lastCategory = categoryLabel;
    const msgs = document.getElementById('chatbot-messages');
    const cats = categories[ROLE] || [];
    const cat  = cats.find(c => c.label === categoryLabel);
    if (!cat) return;
 
    msgs.innerHTML += `<div class="msg user">${categoryLabel}</div>`;
    msgs.innerHTML += `<div class="msg bot">What would you like to know about ${categoryLabel.replace(/[^\w\s]/gi, '').trim()}?</div>`;
 
    const chips = cat.questions.map(q =>
        `<button class="chip" onclick="askQuestion('${q}')">${q}</button>`
    ).join('');
    msgs.innerHTML += `<div class="chat-chips">${chips}</div>`;
    msgs.innerHTML += `<div class="chat-chips"><button class="chip secondary" onclick="showCategories()">← All topics</button></div>`;
    msgs.scrollTop = msgs.scrollHeight;
}
 
function askQuestion(question) {
    const msgs = document.getElementById('chatbot-messages');
    msgs.innerHTML += `<div class="msg user">${question}</div>`;
    msgs.innerHTML += `<div class="msg typing" id="typing">Thinking...</div>`;
    msgs.scrollTop = msgs.scrollHeight;
 
    fetch('{{ route("chatbot.ask") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: question.toLowerCase() })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('typing')?.remove();
        msgs.innerHTML += `<div class="msg bot">${data.reply}</div>`;
        msgs.innerHTML += `<div class="chat-chips">
            ${lastCategory ? `<button class="chip" onclick="showQuestions('${lastCategory}')">Ask another</button>` : ''}
            <button class="chip secondary" onclick="showCategories()">All topics</button>
        </div>`;
        msgs.scrollTop = msgs.scrollHeight;
    })
    .catch(() => {
        document.getElementById('typing')?.remove();
        msgs.innerHTML += `<div class="msg bot">Sorry, something went wrong. Please try again.</div>`;
        msgs.scrollTop = msgs.scrollHeight;
    });
}
 
function sendMessage() {
    const input = document.getElementById('chatbot-input');
    const msgs  = document.getElementById('chatbot-messages');
    const text  = input.value.trim();
    if (!text) return;
    input.value = '';
 
    if (['help', 'topics', 'menu', 'categories'].includes(text.toLowerCase())) {
        msgs.innerHTML += `<div class="msg user">${text}</div>`;
        showCategories();
        return;
    }
 
    msgs.innerHTML += `<div class="msg user">${text}</div>`;
    msgs.innerHTML += `<div class="msg typing" id="typing">Thinking...</div>`;
    msgs.scrollTop = msgs.scrollHeight;
 
    fetch('{{ route("chatbot.ask") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: text.toLowerCase() })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('typing')?.remove();
        msgs.innerHTML += `<div class="msg bot">${data.reply}</div>`;
        if (data.reply.includes('did not understand') || data.reply.includes('I am sorry')) {
            showCategories();
        } else {
            msgs.innerHTML += `<div class="chat-chips">
                ${lastCategory ? `<button class="chip" onclick="showQuestions('${lastCategory}')">Ask another</button>` : ''}
                <button class="chip secondary" onclick="showCategories()">Browse topics</button>
            </div>`;
        }
        msgs.scrollTop = msgs.scrollHeight;
    })
    .catch(() => {
        document.getElementById('typing')?.remove();
        msgs.innerHTML += `<div class="msg bot">Sorry, something went wrong. Please try again.</div>`;
        msgs.scrollTop = msgs.scrollHeight;
    });
}
</script>
 
</body>
</html>