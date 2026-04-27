<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento Estudiantil - Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col shadow-2xl z-20 flex-shrink-0">
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
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>
                
                <a href="{{ route('seguimiento.index') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold shadow-md transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Seguimiento</span>
                </a>

                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Calendario Público</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" onclick="return confirm('¿Seguro que deseas salir?')" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-slate-50">
        
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex justify-between items-center z-10 sticky top-0 flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-black text-[#002845] flex items-center gap-3">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Seguimiento estudiantil
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Busca un estudiante o navega por materia.</p>
            </div>
            
            <a href="{{ route('dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-[#002845] px-5 py-2.5 rounded-xl font-bold transition flex items-center border border-gray-300 shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a Clases
            </a>
        </header>

        <div class="p-6 md:p-8 flex-1">
            <div class="max-w-6xl mx-auto">
                
                @php $errorMsg = $error ?? session('error'); @endphp
                @if($errorMsg)
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 font-medium text-sm">
                        {{ $errorMsg }}
                    </div>
                @endif

                {{-- Mis materias --}}
                @if(isset($horarios) && $horarios->count() > 0)
                <div class="mb-8">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Navegar por materia</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($horarios as $clase)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col gap-3">
                            <div>
                                <div class="font-black text-[#002845] text-sm leading-snug">{{ ucwords(strtolower($clase->curso_nombre)) }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $clase->dia_semana }} · {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}</div>
                            </div>
                            <a href="{{ route('horarios.estudiantes', $clase->id) }}"
                               class="mt-auto inline-flex items-center justify-center gap-1.5 w-full bg-[#002845] text-white text-xs font-bold py-2.5 rounded-xl hover:bg-blue-900 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Ver inscritos
                                @if(isset($clase->seguimientos_count))
                                <span class="bg-white/20 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $clase->seguimientos_count }}</span>
                                @endif
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Búsqueda por cédula --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
                    <div class="bg-[#002845] px-6 py-4 text-white font-bold tracking-wide text-sm">
                        Buscar estudiante por cédula
                    </div>
                    <form action="{{ route('seguimiento.buscar') }}" method="GET" class="p-6 md:p-8">
                        <div class="flex flex-col md:flex-row gap-4">
                            <input type="text" name="cedula" placeholder="Ej: 1001234567" value="{{ request('cedula') }}" required
                                   class="flex-1 bg-gray-50 border-2 border-gray-200 rounded-xl p-4 text-lg font-black focus:border-[#002845] outline-none transition-all shadow-sm">
                            <button type="submit" class="bg-[#C9A227] text-[#002845] px-8 py-4 rounded-xl font-black hover:bg-yellow-500 transition shadow-md md:w-auto w-full flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Buscar
                            </button>
                        </div>
                    </form>
                </div>

                @if(isset($estudiante))
                    <div class="bg-white rounded-3xl shadow-sm border-t-8 border-[#10b981] overflow-hidden border border-gray-200">
                        
                        <div class="p-6 md:p-8 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="flex items-center w-full md:w-auto">
                                <div class="w-16 h-16 bg-[#002845] text-white rounded-xl flex items-center justify-center text-2xl font-black shadow-sm mr-5 flex-shrink-0">
                                    {{ strtoupper(substr($estudiante->nombre_completo, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-2xl font-black text-[#002845] leading-tight">{{ $estudiante->nombre_completo }}</h3>
                                    <p class="text-sm font-bold text-gray-500 mt-1">C.C. {{ $estudiante->cedula }} <span class="mx-2 text-gray-300">|</span> Correo: {{ $estudiante->correo ?? 'No vinculado' }}</p>
                                </div>
                            </div>
                            <div class="text-center md:text-right w-full md:w-auto mt-4 md:mt-0">
                                <span class="block text-[11px] uppercase font-bold text-gray-400 tracking-widest mb-1">Total Asesorías</span>
                                <span class="text-4xl font-black text-[#10b981]">{{ $historial->count() }}</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Fecha / Clase</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Calificación al Docente</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Reporte de Evolución</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($historial as $reserva)
                                        <tr class="hover:bg-gray-50/80 transition-colors duration-200">
                                            
                                            <td class="px-6 py-5">
                                                <div class="font-black text-[#002845] text-base mb-1">{{ ucwords(strtolower($reserva->horario->curso_nombre ?? 'Clase eliminada')) }}</div>
                                                <div class="text-xs font-semibold text-gray-500 flex flex-col gap-1">
                                                    <span class="flex items-center"><span class="mr-1">📅</span>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</span>
                                                    <span class="flex items-center"><span class="mr-1">👨‍🏫</span>{{ $reserva->horario->docente_nombre ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                                @if($reserva->estado === 'Evaluada')
                                                    @if($reserva->asistencia)
                                                        <span class="bg-green-50 text-green-700 text-xs px-3 py-1.5 rounded-full font-bold border border-green-200 shadow-sm">✔️ Asistió</span>
                                                    @else
                                                        <span class="bg-red-50 text-red-700 text-xs px-3 py-1.5 rounded-full font-bold border border-red-200 shadow-sm">❌ Faltó</span>
                                                    @endif
                                                @else
                                                    <span class="bg-yellow-50 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-bold border border-yellow-200 shadow-sm">⏳ Pendiente</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                                @if($reserva->calificacion)
                                                    <div class="inline-flex bg-yellow-50 px-3 py-1.5 rounded-full border border-yellow-100 shadow-sm" title="{{ $reserva->calificacion }} Estrellas">
                                                        <span class="text-sm tracking-widest">{{ str_repeat('⭐', $reserva->calificacion) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-xs font-semibold text-gray-400 italic">Sin calificar</span>
                                                @endif
                                            </td>

                                            <td class="px-6 py-5">
                                                @if($reserva->evolucion)
                                                    <div class="bg-white p-3.5 rounded-xl border border-gray-200 italic text-sm text-gray-600 shadow-sm relative">
                                                        <span class="text-gray-300 text-2xl absolute top-1 left-2">"</span>
                                                        <p class="pl-5 pr-2 relative z-10 font-medium">{{ $reserva->evolucion }}</p>
                                                    </div>
                                                @else
                                                    <span class="text-xs font-semibold text-gray-400 italic">Sin reporte registrado.</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                                <span class="text-4xl block mb-3 opacity-50">📭</span>
                                                <p class="font-bold text-lg">No hay registros</p>
                                                <p class="text-sm mt-1">Este estudiante no tiene historial de asesorías registradas.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </main>

</body>
</html>