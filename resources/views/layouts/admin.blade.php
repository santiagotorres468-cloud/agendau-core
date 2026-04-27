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
            width: 260px;
            background: var(--ink-800);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
            flex-shrink: 0;
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
            .au-sidebar { display: none; }
            .au-content { padding: 20px; }
            .au-pageheader { padding: 18px 20px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="au-shell">

    {{-- ═══════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════ --}}
    <aside class="au-sidebar">
        <div class="au-sidebar-brand">
            <div class="au-sidebar-brand-logo">A</div>
            <div>
                <div class="au-sidebar-brand-name">Agenda U</div>
                <div class="au-sidebar-brand-tag">Sistema de asesorías</div>
            </div>
        </div>

        <nav style="padding-bottom: 16px;">
            <div class="au-sidebar-section-title">Principal</div>

            <a href="{{ route('dashboard') }}" class="au-sidebar-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Panel principal
            </a>

            <a href="{{ route('seguimiento.index') }}" class="au-sidebar-link {{ request()->routeIs('seguimiento.*') ? 'is-active' : '' }}">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Seguimiento
            </a>

            <a href="/" class="au-sidebar-link">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Calendario público
            </a>

            @auth
                @if(auth()->user()->rol === 'admin')
                    <div class="au-sidebar-section-title">Administración</div>

                    <a href="{{ route('usuarios.index') }}" class="au-sidebar-link {{ request()->routeIs('usuarios.*') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 100-8 4 4 0 000 8z"/></svg>
                        Usuarios
                    </a>

                    <a href="{{ route('admin.encuestas') }}" class="au-sidebar-link {{ request()->routeIs('admin.encuestas') ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Encuestas e informes
                    </a>
                @endif
            @endauth
        </nav>

        @auth
            <div class="au-sidebar-user">
                <div class="au-sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div style="flex: 1; min-width: 0;">
                    <div class="au-sidebar-user-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="au-sidebar-user-role">
                        {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Cerrar sesión" style="background: none; border: none; cursor: pointer; color: rgba(255,255,255,0.5); padding: 4px;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- ═══════════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════════ --}}
    <main class="au-main">

        <header class="au-pageheader">
            <div>
                <h1 class="au-pageheader-title">{{ $pageTitle ?? 'Panel principal' }}</h1>
                @isset($pageSubtitle)
                    <p class="au-pageheader-subtitle">{{ $pageSubtitle }}</p>
                @endisset
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
</div>

@stack('scripts')
</body>
</html>