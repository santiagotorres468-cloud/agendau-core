{{-- resources/views/layouts/admin-layout.blade.php
     Layout maestro: tipografía Manrope, sistema de colores unificado,
     sidebar consistente y header. Toda vista de admin/profesor lo usa.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Agenda U' }} — Sistema de Asesorías</title>

    {{-- Tipografía: Manrope (moderna, profesional, soporta tildes) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind para utilidades --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ════════════════════════════════════════════════════════
           DESIGN TOKENS — Sistema de diseño Agenda U
           Cambiar aquí actualiza todas las vistas a la vez
        ════════════════════════════════════════════════════════ */
        :root {
            /* Marca */
            --ink-900: #0A1628;
            --ink-800: #002845;     /* Color principal de marca */
            --ink-700: #003A5C;
            --ink-600: #1A4D78;
            --ink-100: #DCE8F2;
            --ink-50:  #F0F5FA;

            /* Acento (dorado refinado, no el amarillo brillante) */
            --accent-600: #A87E1A;
            --accent-500: #C9A227;
            --accent-400: #DBB54A;
            --accent-100: #F5E9C7;
            --accent-50:  #FBF6E6;

            /* Neutros */
            --surface-0:   #FFFFFF;
            --surface-50:  #F8FAFC;
            --surface-100: #F1F5F9;
            --surface-200: #E2E8F0;
            --surface-300: #CBD5E1;

            --text-900: #0F172A;
            --text-700: #334155;
            --text-500: #64748B;
            --text-400: #94A3B8;

            /* Estado */
            --success-700: #047857;
            --success-600: #059669;
            --success-50:  #ECFDF5;
            --danger-700:  #991B1B;
            --danger-600:  #B91C1C;
            --danger-50:   #FEF2F2;
            --warning-700: #92400E;
            --warning-600: #B45309;
            --warning-50:  #FFFBEB;

            /* Tipografía */
            --font-display: 'Manrope', system-ui, sans-serif;
            --font-body:    'Manrope', system-ui, sans-serif;

            /* Sombras */
            --shadow-sm:  0 1px 2px rgba(10, 22, 40, 0.04);
            --shadow-md:  0 4px 12px rgba(10, 22, 40, 0.06);
            --shadow-lg:  0 8px 24px rgba(10, 22, 40, 0.08);

            /* Bordes */
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 20px;
        }

        * { box-sizing: border-box; }

        html, body {
            font-family: var(--font-body);
            color: var(--text-900);
            background: var(--surface-50);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
        }

        /* Encabezados con tracking refinado */
        h1, h2, h3, h4 {
            font-family: var(--font-display);
            letter-spacing: -0.02em;
            color: var(--ink-800);
        }

        /* ════════════════════════════════════════════════════════
           COMPONENTES BASE (clases utilitarias propias)
        ════════════════════════════════════════════════════════ */

        /* Sidebar */
        .au-sidebar {
            width: 256px;
            background: var(--ink-800);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
            flex-shrink: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .au-sidebar-brand {
            padding: 24px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .au-sidebar-brand-logo {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: var(--accent-500);
            color: var(--ink-800);
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 18px;
        }

        .au-sidebar-brand-name {
            font-size: 18px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.02em;
        }

        .au-sidebar-brand-tag {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            font-weight: 500;
            margin-top: 2px;
        }

        .au-sidebar-section-title {
            padding: 20px 24px 8px;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .au-sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 24px;
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .au-sidebar-link:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }
        .au-sidebar-link.is-active {
            background: rgba(255,255,255,0.08);
            color: white;
            border-left-color: var(--accent-500);
            font-weight: 600;
        }

        .au-sidebar-link svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .au-sidebar-user {
            margin-top: auto;
            padding: 18px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .au-sidebar-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--ink-600);
            color: white;
            display: grid;
            place-items: center;
            font-weight: 700;
            font-size: 14px;
        }

        .au-sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: white;
            line-height: 1.3;
        }

        .au-sidebar-user-role {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            font-weight: 500;
        }

        /* Layout principal */
        .au-shell {
            display: flex;
            min-height: 100vh;
        }
        .au-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Header de página */
        .au-pageheader {
            background: white;
            border-bottom: 1px solid var(--surface-200);
            padding: 22px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .au-pageheader-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--ink-800);
            letter-spacing: -0.025em;
        }
        .au-pageheader-subtitle {
            font-size: 13px;
            color: var(--text-500);
            margin-top: 2px;
            font-weight: 500;
        }

        .au-content {
            flex: 1;
            padding: 32px;
        }

        /* Tarjetas */
        .au-card {
            background: white;
            border: 1px solid var(--surface-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .au-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--surface-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .au-card-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--ink-800);
        }
        .au-card-body {
            padding: 24px;
        }

        /* Botones */
        .au-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: 13px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            line-height: 1;
            font-family: inherit;
        }
        .au-btn-primary {
            background: var(--ink-800);
            color: white;
        }
        .au-btn-primary:hover {
            background: var(--ink-700);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }
        .au-btn-accent {
            background: var(--accent-500);
            color: var(--ink-800);
        }
        .au-btn-accent:hover {
            background: var(--accent-400);
        }
        .au-btn-ghost {
            background: white;
            color: var(--text-700);
            border-color: var(--surface-200);
        }
        .au-btn-ghost:hover {
            background: var(--surface-50);
            color: var(--ink-800);
        }
        .au-btn-danger {
            background: var(--danger-50);
            color: var(--danger-700);
            border-color: #FECACA;
        }
        .au-btn-danger:hover {
            background: var(--danger-600);
            color: white;
            border-color: var(--danger-600);
        }
        .au-btn-sm { padding: 6px 12px; font-size: 12px; }

        /* Chips de estado */
        .au-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .au-chip-success { background: var(--success-50); color: var(--success-700); }
        .au-chip-danger  { background: var(--danger-50);  color: var(--danger-700); }
        .au-chip-warning { background: var(--warning-50); color: var(--warning-700); }
        .au-chip-info    { background: var(--ink-50);     color: var(--ink-800); }
        .au-chip-neutral { background: var(--surface-100);color: var(--text-700); }

        /* Inputs */
        .au-input,
        .au-select,
        .au-textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--surface-200);
            border-radius: var(--radius-md);
            font-size: 14px;
            font-family: inherit;
            color: var(--text-900);
            background: white;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .au-input:focus,
        .au-select:focus,
        .au-textarea:focus {
            outline: none;
            border-color: var(--ink-800);
            box-shadow: 0 0 0 3px rgba(0, 40, 69, 0.1);
        }
        .au-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-700);
            margin-bottom: 6px;
            letter-spacing: 0.01em;
        }

        /* Tabla */
        .au-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        .au-table thead th {
            background: var(--surface-50);
            padding: 12px 18px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-500);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid var(--surface-200);
        }
        .au-table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--surface-100);
            color: var(--text-700);
        }
        .au-table tbody tr:hover {
            background: var(--surface-50);
        }
        .au-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Alertas */
        .au-alert {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        .au-alert-success { background: var(--success-50); color: var(--success-700); border-left: 3px solid var(--success-600); }
        .au-alert-danger  { background: var(--danger-50);  color: var(--danger-700);  border-left: 3px solid var(--danger-600); }
        .au-alert-warning { background: var(--warning-50); color: var(--warning-700); border-left: 3px solid var(--warning-600); }
        .au-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }

        /* Empty states */
        .au-empty {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-500);
        }
        .au-empty svg { width: 40px; height: 40px; color: var(--text-400); margin: 0 auto 12px; }
        .au-empty-title { font-size: 14px; font-weight: 600; color: var(--text-700); margin-bottom: 4px; }
        .au-empty-text { font-size: 13px; color: var(--text-500); }

        /* Tabs */
        .au-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid var(--surface-200);
            padding: 0 24px;
        }
        .au-tab {
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-500);
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            font-family: inherit;
        }
        .au-tab:hover { color: var(--text-700); }
        .au-tab.is-active {
            color: var(--ink-800);
            border-bottom-color: var(--ink-800);
        }

        /* Stat boxes */
        .au-stat {
            background: white;
            border: 1px solid var(--surface-200);
            border-radius: var(--radius-lg);
            padding: 20px;
        }
        .au-stat-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-500);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
        .au-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--ink-800);
            letter-spacing: -0.025em;
        }
        .au-stat-meta {
            font-size: 12px;
            color: var(--text-500);
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .au-content { padding: 20px; }
            .au-pageheader { padding: 14px 16px; }
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="flex h-screen overflow-hidden bg-gray-50 text-gray-800" x-data="{ sidebarOpen: false }">

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 md:hidden" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="w-64 bg-[#002845] text-white flex flex-col fixed md:relative inset-y-0 left-0 z-40 shadow-2xl transition-transform duration-300 md:translate-x-0 flex-shrink-0">

        {{-- Logo --}}
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <div class="w-9 h-9 rounded-lg bg-[#C9A227] text-[#002845] font-extrabold text-sm flex items-center justify-center flex-shrink-0 tracking-tight">AU</div>
            <div>
                <p class="text-base font-bold text-white tracking-tight leading-tight">Agenda U</p>
                <p class="text-xs text-white/50 font-medium">Sistema de Asesorías</p>
            </div>
        </div>

        {{-- Usuario --}}
        @auth
        <div class="p-6 pb-2">
            <p class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-2">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center text-white text-sm font-bold shadow-inner uppercase flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm text-white truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                        {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}
                    </p>
                </div>
            </div>

            {{-- Nav principal --}}
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>

                @if(auth()->user()->rol === 'admin')
                    <a href="{{ route('admin.encuestas') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span>Satisfacción</span>
                    </a>
                @else
                    <a href="{{ route('seguimiento.index') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Seguimiento</span>
                    </a>
                @endif

                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Calendario Público</span>
                </a>
            </nav>
        </div>
        @endauth

        {{-- Cerrar sesión --}}
        @auth
        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">

        <header class="au-pageheader">
            <div style="display:flex; align-items:center; gap:12px;">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="#002845" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="au-pageheader-title">{{ $pageTitle ?? 'Panel principal' }}</h1>
                    @isset($pageSubtitle)
                        <p class="au-pageheader-subtitle">{{ $pageSubtitle }}</p>
                    @endisset
                </div>
            </div>
            @isset($pageActions)
                <div style="display: flex; gap: 10px;">{{ $pageActions }}</div>
            @endisset
        </header>

        <div class="au-content">
            {{-- Mensajes flash --}}
            @if (session('exito'))
                <div class="au-alert au-alert-success">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ session('exito') }}
                </div>
            @endif
            @if (session('error'))
                <div class="au-alert au-alert-danger">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

@stack('scripts')
</body>
</html>