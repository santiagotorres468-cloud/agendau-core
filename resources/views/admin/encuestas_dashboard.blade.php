<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satisfacción Estudiantil - Agenda U</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col hidden md:flex shadow-2xl z-20 flex-shrink-0">
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <div class="w-9 h-9 rounded-lg bg-[#C9A227] text-[#002845] font-extrabold text-sm flex items-center justify-center flex-shrink-0 tracking-tight">AU</div>
            <div>
                <p class="text-base font-bold text-white tracking-tight leading-tight">Agenda U</p>
                <p class="text-xs text-white/50 font-medium">Sistema de Asesorías</p>
            </div>
        </div>

        <div class="p-6">
            <p class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-2">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-[#C9A227] flex items-center justify-center text-[#002845] text-sm font-bold shadow-inner uppercase">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-sm truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                        Administrador
                    </p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>
                <a href="{{ route('admin.encuestas') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold shadow-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    <span>Satisfacción</span>
                </a>
                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Calendario Público</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('¿Seguro que deseas salir?')" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-slate-50">

        <header class="bg-white px-8 py-6 shadow-sm border-b border-gray-200 sticky top-0 z-10 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-[#002845] flex items-center gap-3">
                    <svg class="w-7 h-7 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Satisfacción estudiantil
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Resumen de encuestas por docente y por materia</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#002845] bg-gray-100 hover:bg-gray-200 px-4 py-2.5 rounded-xl border border-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Volver al panel
            </a>
        </header>

        <div class="p-6 md:p-8 space-y-10">
            <div class="max-w-7xl mx-auto space-y-10">

                {{-- ─── POR DOCENTE ─────────────────────────────────────── --}}
                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Por docente</h3>

                    @if($docentesConPromedio->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400 text-sm font-medium">
                            No hay docentes con encuestas registradas todavía.
                        </div>
                    @else
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-[#002845] text-white">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold">Docente</th>
                                        <th class="px-6 py-3 text-center font-semibold">Encuestas</th>
                                        <th class="px-6 py-3 text-center font-semibold">Promedio</th>
                                        <th class="px-6 py-3 text-center font-semibold">Nivel</th>
                                        <th class="px-6 py-3 text-right font-semibold">Informe PDF</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($docentesConPromedio->sortByDesc('promedio_encuesta') as $docente)
                                        @php
                                            $p     = $docente->promedio_encuesta;
                                            $pct   = ($p / 5) * 100;
                                            $color = $p >= 4.5 ? '#059669' : ($p >= 3.5 ? '#C9A227' : '#B91C1C');
                                            $nivel = $p >= 4.5 ? 'Excelente' : ($p >= 3.5 ? 'Bueno' : ($p >= 2.5 ? 'Regular' : 'Por mejorar'));
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-[#002845] flex items-center justify-center text-xs font-bold flex-shrink-0" style="color:#C9A227">
                                                        {{ strtoupper(substr($docente->name, 0, 1)) }}
                                                    </div>
                                                    <span class="font-semibold text-gray-900">{{ $docente->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-500">
                                                @if($docente->total_encuestas > 0)
                                                    {{ $docente->total_encuestas }}
                                                @else
                                                    <span class="text-gray-300">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($docente->total_encuestas > 0)
                                                    <div class="flex items-center justify-center gap-3">
                                                        <div class="w-28 bg-gray-100 rounded-full h-2">
                                                            <div class="h-2 rounded-full" style="width: {{ $pct }}%; background: {{ $color }};"></div>
                                                        </div>
                                                        <span class="font-bold text-sm w-8" style="color: {{ $color }};">{{ number_format($p, 1) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-300 text-xs">Sin datos</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($docente->total_encuestas > 0)
                                                    <span class="text-xs font-bold px-2.5 py-1 rounded-full" style="background: {{ $color }}22; color: {{ $color }};">
                                                        {{ $nivel }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300 text-xs">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($docente->total_encuestas > 0)
                                                    <form action="{{ route('reportes.docente') }}" method="GET" target="_blank" class="inline">
                                                        <input type="hidden" name="docente_id" value="{{ $docente->id }}">
                                                        <button type="submit" class="inline-flex items-center gap-1.5 bg-[#002845] text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-[#003A5C] transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            Descargar PDF
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-300 text-xs">Sin encuestas</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                {{-- ─── POR MATERIA ─────────────────────────────────────── --}}
                <section>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Por materia</h3>

                    @if($cursos->isEmpty())
                        <div class="bg-white rounded-2xl border border-gray-100 p-10 text-center text-gray-400 text-sm font-medium">
                            No hay materias registradas todavía.
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($cursos as $curso)
                                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col gap-4">
                                    <div class="font-bold text-[#002845] text-sm leading-snug">
                                        {{ ucwords(strtolower($curso)) }}
                                    </div>
                                    <form action="{{ route('reportes.curso') }}" method="GET" target="_blank" class="mt-auto">
                                        <input type="hidden" name="curso_nombre" value="{{ $curso }}">
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 border border-[#002845] text-[#002845] text-xs font-bold px-4 py-2 rounded-lg hover:bg-[#002845] hover:text-white transition">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Generar informe
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>
        </div>
    </main>

</body>
</html>
