<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        :root{--bg:#f3f5f8;--panel:#fff;--ink:#1e293b;--muted:#697586;--edge:#dfe4ea;--soft:#f6f8fa;--primary:#2c465e;--primarysoft:#eaf0f5;--danger:#b42318;--success:#0f766e;--warn:#92400e;--shadow:0 3px 18px rgba(20,35,50,.05);--radius:14px}
        *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:14px/1.55 system-ui,-apple-system,Segoe UI,Arial,sans-serif}
        a{color:inherit;text-decoration:none}button,input,textarea,select{font:inherit}
        .app{min-height:100vh;display:flex}
        .sidebar{background:#fff;border-right:1px solid var(--edge);width:235px;flex-shrink:0;padding:22px 15px;display:flex;flex-direction:column;gap:18px;position:sticky;top:0;height:100vh}
        .brand{display:flex;align-items:center;gap:11px;padding:5px 9px}.brand-icon{display:grid;place-items:center;width:40px;height:40px;background:var(--primary);color:#fff;border-radius:11px;font-size:20px}
        .brand strong{display:block;line-height:1.2;font-size:15px}.brand span{font-size:11px;color:var(--muted)}
        .nav-title{font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:#8a96a3;padding:0 12px;margin-bottom:8px}
        .nav{display:flex;flex-direction:column;gap:4px}
        .nav a{background:transparent;border:0;border-radius:9px;color:#596879;text-align:left;padding:11px 12px;display:flex;gap:11px;align-items:center;font-weight:570}
        .nav a:hover,.nav a.active{background:var(--primarysoft);color:#1d344b}.nav a.active{font-weight:700}
        .symbol{font-size:17px;width:20px;text-align:center}
        .sidebar-bottom{margin-top:auto;border:1px solid var(--edge);border-radius:11px;padding:11px;background:var(--soft)}
        .sidebar-bottom strong{font-size:12px;display:block}.sidebar-bottom span{font-size:11px;color:var(--muted)}
        .main{min-width:0;flex:1;display:flex;flex-direction:column}
        .topbar{height:70px;border-bottom:1px solid var(--edge);background:#fff;display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:10}
        .topbar strong{font-size:15px}.local{background:#f4f7f9;border:1px solid var(--edge);border-radius:999px;padding:6px 10px;font-size:12px;color:#4b5f73}
        .content{padding:24px 28px 40px}
        .page-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
        h1{margin:0;font-size:24px;line-height:1.25;letter-spacing:-.5px} h2{margin:0;font-size:18px} h3{margin:0;font-size:14px}
        .sub{color:var(--muted);font-size:13px;margin-top:4px}
        .card{background:var(--panel);border:1px solid var(--edge);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
        .grid{display:grid;gap:16px}.grid.two{grid-template-columns:repeat(2,minmax(0,1fr))}.grid.three{grid-template-columns:repeat(3,minmax(0,1fr))}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--edge);background:#fff;color:var(--ink);border-radius:10px;padding:9px 14px;font-weight:600;cursor:pointer}
        .btn:hover{background:#f8fafc}.btn.primary{background:var(--primary);border-color:var(--primary);color:#fff}.btn.primary:hover{filter:brightness(1.05)}
        .btn.danger{background:#fff;border-color:#fecaca;color:#b42318}.btn.small{padding:6px 10px;font-size:12px;border-radius:8px}
        .btn:disabled{opacity:.45;cursor:not-allowed}
        .btn-row{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
        .pill{display:inline-flex;align-items:center;border-radius:999px;padding:4px 10px;font-size:12px;font-weight:600}
        .pill.draft{background:#fff7ed;color:#9a3412;border:1px solid #fed7aa}.pill.final{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
        .field{display:flex;flex-direction:column;gap:6px}.field label{font-size:13px;font-weight:600;color:#334155}
        .input, .select, .textarea{width:100%;border:1px solid #cfd8e3;border-radius:10px;padding:10px 12px;background:#fff;color:var(--ink)}
        .textarea{min-height:110px;resize:vertical}.help{font-size:12px;color:var(--muted)}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.form-grid .wide{grid-column:1/-1}
        .table{width:100%;border-collapse:collapse}.table th,.table td{padding:11px 10px;border-bottom:1px solid var(--edge);text-align:left;font-size:13px;vertical-align:top}
        .table th{font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:#64748b}
        .alert{border-radius:10px;padding:10px 12px;font-size:13px;margin-bottom:12px}
        .alert.error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}.alert.success{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46}.alert.warn{background:#fffbeb;border:1px solid #fde68a;color:#92400e}
        .stepper{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
        .step{border:1px solid var(--edge);background:#fff;border-radius:10px;padding:8px 12px;font-size:13px;color:#475569}
        .step.active{background:var(--primary);border-color:var(--primary);color:#fff;font-weight:700}
        .metric{font-size:28px;font-weight:700;margin-top:6px}.label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px}
        .list-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--edge)}
        .muted{color:var(--muted)} .section-space{margin-top:16px}
        @media (max-width: 980px){.app{flex-direction:column}.sidebar{position:static;width:auto;height:auto;flex-direction:row;flex-wrap:wrap}.main{width:100%}.grid.two,.grid.three,.form-grid{grid-template-columns:1fr}.content{padding:18px}}
    </style>
    @livewireStyles
</head>
<body>
@php
    $nav = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '▦', 'match' => 'dashboard'],
        ['route' => 'letters.create', 'label' => 'Buat Surat', 'icon' => '✎', 'match' => 'letters.create'],
        ['route' => 'letters.index', 'label' => 'Riwayat Surat', 'icon' => '◷', 'match' => 'letters.*'],
        ['route' => 'settings', 'label' => 'Pengaturan', 'icon' => '⚙', 'match' => 'settings'],
    ];
@endphp
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">▤</div>
            <div><strong>Letter Generator</strong><span>Personal Edition</span></div>
        </div>
        <div style="flex:1">
            <div class="nav-title">Menu utama</div>
            <nav class="nav">
                @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}" class="{{ request()->routeIs($item['match']) && !($item['route']==='letters.index' && request()->routeIs('letters.create','letters.edit')) ? 'active' : '' }}">
                        <span class="symbol">{{ $item['icon'] }}</span>{{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="sidebar-bottom">
            <strong>◉ Mode Lokal</strong>
            <span>Localhost · Single user · {{ config('app.timezone') }}</span>
        </div>
    </aside>
    <div class="main">
        <header class="topbar">
            <strong>{{ $headerTitle ?? config('app.name') }}</strong>
            <span class="local">⌂ 127.0.0.1 · {{ strtoupper(config('app.locale')) }}</span>
        </header>
        <main class="content">
            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert warn">{{ session('warning') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>
