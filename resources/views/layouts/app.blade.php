<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\Setting::get('system_name', 'MakaziLink v2')) — Rental Management</title>
 
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
 
        .nav-section-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            cursor: pointer;
            user-select: none;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            color: rgba(255,255,255,.65);
            font-size: .85rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-top: 2px;
        }
 
        .nav-section-btn .section-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
 
        .nav-section-btn .section-left i {
            width: 18px;
            text-align: center;
            font-size: .95rem;
            opacity: .8;
        }
 
        .nav-section-btn .section-arrow {
            font-size: .7rem;
            color: rgba(255,255,255,.3);
            transition: transform .2s;
        }
 
        .nav-section-btn.collapsed .section-arrow {
            transform: rotate(-90deg);
        }
 
        .nav-section-btn:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
        }
 
        .nav-section-btn.active-group {
            background: rgba(255,255,255,.07);
            color: #fff;
            border-left: 3px solid var(--ml-green);
            padding-left: 17px;
        }
 
        .nav-group {
            overflow: hidden;
            transition: max-height .25s ease;
            background: rgba(0,0,0,.12);
        }
 
        .nav-group .nav-link {
            padding-left: 48px !important;
        }
 
        .nav-group .nav-link.active {
            padding-left: 45px !important;
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
 
        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,.06);
            margin: 6px 20px;
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
 
        #chatbot-bubble {
            position: fixed;
            bottom: 90px;
            right: 88px;
            background: #0f2d1e;
            color: #fff;
            padding: 8px 14px;
            border-radius: 12px 12px 0 12px;
            font-size: .78rem;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            z-index: 9998;
            animation: fadeInBubble .4s ease;
            white-space: nowrap;
        }
 
        #chatbot-bubble::after {
            content: '';
            position: absolute;
            bottom: -6px;
            right: 10px;
            width: 12px;
            height: 12px;
            background: #0f2d1e;
            clip-path: polygon(0 0, 100% 0, 100% 100%);
        }
 
        @keyframes fadeInBubble {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
 
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
 
        .chip:hover { background: #1a7a4a; color: #fff; }
 
        .chip.secondary { border-color: #e9ecef; color: #6c757d; }
        .chip.secondary:hover { background: #f4f6f8; color: #1a1a2e; }
 
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
            @php $logoPath = \App\Models\Setting::get('logo_path', ''); @endphp
            @if($logoPath)
                <img src="{{ asset('storage/' . $logoPath) }}"
                     alt="Logo"
                     style="height:34px;width:34px;object-fit:cover;border-radius:8px;flex-shrink:0">
            @else
                <span class="logo-icon"><i class="bi bi-buildings text-white"></i></span>
            @endif
            {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }}
        </a>
        <div class="brand-sub">Rental Management System</div>
    </div>
 
    <div class="sidebar-nav mt-2">
 
        {{-- Overview Group: Dashboard + Users --}}
        <button class="nav-section-btn {{ request()->routeIs('dashboard') || request()->routeIs('settings.users') ? 'active-group' : '' }}"
                id="btn-group-overview"
                onclick="toggleGroup('group-overview', 'btn-group-overview')">
            <span class="section-left">
                <i class="bi bi-grid-1x2"></i> Overview
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-overview">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            @hasrole(['admin', 'caretaker'])
            <a href="{{ route('messages.index') }}"
               class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots"></i> Messages
                <span id="msg-badge" style="display:none;background:#e74c3c;color:#fff;border-radius:20px;padding:1px 7px;font-size:.65rem;font-weight:700;margin-left:auto"></span>
            </a>
            @endhasrole
            @hasrole(['admin'])
            <a href="{{ route('settings.users') }}"
               class="nav-link {{ request()->routeIs('settings.users') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Users
            </a>
            @endhasrole
        </div>
 
        <div class="nav-divider"></div>
 
        @hasrole(['admin', 'agent'])
        {{-- Properties Group --}}
        <button class="nav-section-btn {{ request()->routeIs('properties.*') || request()->routeIs('units.*') || request()->routeIs('tenants.*') || request()->routeIs('leases.*') ? 'active-group' : '' }}"
                id="btn-group-properties"
                onclick="toggleGroup('group-properties', 'btn-group-properties')">
            <span class="section-left">
                <i class="bi bi-building"></i> Properties
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-properties">
            <a href="{{ route('properties.index') }}"
               class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> All Properties
            </a>
            <a href="{{ route('units.index') }}"
               class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i> Units
            </a>
            <a href="{{ route('tenants.index') }}"
               class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Tenants
            </a>
            <a href="{{ route('leases.index') }}"
               class="nav-link {{ request()->routeIs('leases.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Leases
            </a>
        </div>
        @endhasrole
 
        @hasrole(['caretaker'])
        <button class="nav-section-btn {{ request()->routeIs('units.*') || request()->routeIs('tenants.*') ? 'active-group' : '' }}"
                id="btn-group-caretaker"
                onclick="toggleGroup('group-caretaker', 'btn-group-caretaker')">
            <span class="section-left">
                <i class="bi bi-building"></i> Properties
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-caretaker">
            <a href="{{ route('units.index') }}"
               class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-door-open"></i> Units
            </a>
            <a href="{{ route('tenants.index') }}"
               class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Tenants
            </a>
        </div>
        @endhasrole
 
        @hasrole(['admin', 'accountant'])
        {{-- Finance Group --}}
        <button class="nav-section-btn {{ request()->routeIs('payments.*') || request()->routeIs('invoices.*') || request()->routeIs('reports.*') ? 'active-group' : '' }}"
                id="btn-group-finance"
                onclick="toggleGroup('group-finance', 'btn-group-finance')">
            <span class="section-left">
                <i class="bi bi-cash-coin"></i> Finance
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-finance">
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
            <a href="{{ route('reports.profit-loss') }}"
               class="nav-link {{ request()->routeIs('reports.profit-loss*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Profit & Loss
            </a>
        </div>
        @endhasrole
 
        @hasrole(['admin', 'caretaker'])
        {{-- Operations Group --}}
        <button class="nav-section-btn {{ request()->routeIs('maintenance.*') || request()->routeIs('water.*') ? 'active-group' : '' }}"
                id="btn-group-operations"
                onclick="toggleGroup('group-operations', 'btn-group-operations')">
            <span class="section-left">
                <i class="bi bi-tools"></i> Operations
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-operations">
            <a href="{{ route('maintenance.index') }}"
               class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Maintenance
            </a>
            <a href="{{ route('water.index') }}"
               class="nav-link {{ request()->routeIs('water.*') ? 'active' : '' }}">
                <i class="bi bi-droplet"></i> Water Readings
            </a>
        </div>
        @endhasrole
 
        @hasrole(['accountant'])
        <a href="{{ route('water.index') }}"
           class="nav-link {{ request()->routeIs('water.*') ? 'active' : '' }}">
            <i class="bi bi-droplet"></i> Water Readings
        </a>
        @endhasrole
 
        @hasrole(['admin'])
        <div class="nav-divider"></div>
        {{-- Settings Group --}}
        <button class="nav-section-btn {{ request()->routeIs('settings.*') || request()->routeIs('subscription.*') ? 'active-group' : '' }}"
                id="btn-group-settings"
                onclick="toggleGroup('group-settings', 'btn-group-settings')">
            <span class="section-left">
                <i class="bi bi-gear"></i> Settings
            </span>
            <i class="bi bi-chevron-down section-arrow"></i>
        </button>
        <div class="nav-group" id="group-settings">
            <a href="{{ route('settings.index') }}"
               class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> System Settings
            </a>
            <a href="{{ route('subscription.index') }}"
               class="nav-link {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Subscription
            </a>
        </div>
        
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
        <div class="d-flex align-items-center gap-2">
            <div style="width:32px;height:32px;background:#e8f5ee;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a7a4a;flex-shrink:0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <span style="font-size:.82rem;font-weight:600;color:#1a1a2e">
                {{ explode(' ', auth()->user()->name)[0] }}
            </span>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit"
                    style="background:none;border:1.5px solid #e9ecef;border-radius:8px;color:#6c757d;font-size:.78rem;cursor:pointer;padding:5px 12px;display:flex;align-items:center;gap:5px"
                    onmouseover="this.style.borderColor='#e74c3c';this.style.color='#e74c3c'"
                    onmouseout="this.style.borderColor='#e9ecef';this.style.color='#6c757d'">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
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
 
{{-- Chatbot Bubble --}}
<div id="chatbot-bubble">👋 Need help? Ask me!</div>
 
{{-- Chatbot Widget --}}
<button id="chatbot-btn" onclick="toggleChatbot()" title="Ask Assistant">
    <i class="bi bi-robot"></i>
</button>
 
<div id="chatbot-box">
    <div class="chatbot-header">
        <div class="title">
            <i class="bi bi-robot"></i> {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }} Assistant
        </div>
        <button class="close-btn" onclick="toggleChatbot()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="chatbot-messages" id="chatbot-messages">
        <div class="msg bot">Hi {{ auth()->user()->name }}! 👋 Welcome to the {{ \App\Models\Setting::get('system_name', 'MakaziLink v2') }} Assistant.
 
I can help you with properties, tenants, payments, maintenance and more.
 
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
 
// Hide bubble after 4 seconds
setTimeout(() => {
    const bubble = document.getElementById('chatbot-bubble');
    if (bubble) {
        bubble.style.transition = 'opacity .5s';
        bubble.style.opacity = '0';
        setTimeout(() => bubble.remove(), 500);
    }
}, 4000);
 
// Sidebar dropdown — only one open at a time
function toggleGroup(groupId, btnId) {
    const allGroups   = document.querySelectorAll('.nav-group');
    const allBtns     = document.querySelectorAll('.nav-section-btn');
    const targetGroup = document.getElementById(groupId);
    const targetBtn   = document.getElementById(btnId);
    const isOpen      = targetGroup.style.maxHeight && targetGroup.style.maxHeight !== '0px';
 
    // Close all
    allGroups.forEach(g => g.style.maxHeight = '0px');
    allBtns.forEach(b => b.classList.add('collapsed'));
 
    // Open target if it was closed
    if (!isOpen) {
        targetGroup.style.maxHeight = targetGroup.scrollHeight + 'px';
        targetBtn.classList.remove('collapsed');
    }
}
 
// Auto open active group on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-group').forEach(group => {
        const hasActive = group.querySelector('.nav-link.active');
        const btn       = group.previousElementSibling;
        if (hasActive) {
            group.style.maxHeight = group.scrollHeight + 'px';
            if (btn && btn.classList.contains('nav-section-btn')) {
                btn.classList.remove('collapsed');
            }
        } else {
            group.style.maxHeight = '0px';
            if (btn && btn.classList.contains('nav-section-btn')) {
                btn.classList.add('collapsed');
            }
        }
    });
});
 
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
    const box    = document.getElementById('chatbot-box');
    const bubble = document.getElementById('chatbot-bubble');
    if (bubble) bubble.remove();
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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
 
// Poll unread message count every 30 seconds
@hasrole(['admin', 'caretaker'])
function fetchUnreadCount() {
    fetch('{{ route("messages.unread") }}')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('msg-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(() => {});
}
fetchUnreadCount();
setInterval(fetchUnreadCount, 30000);
@endhasrole

// Prevent scroll from changing number inputs
document.addEventListener('wheel', function(e) {
    if (document.activeElement.type === 'number') {
        document.activeElement.blur();
    }
});

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
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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