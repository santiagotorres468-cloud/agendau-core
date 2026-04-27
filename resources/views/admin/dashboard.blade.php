<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión - Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    @php
        $estudianteBuscado = null;
        $historialBuscado = collect();
        $cedula = request('cedula');
        $errorBusqueda = null;

        if($cedula) {
            $estudianteBuscado = \App\Models\Estudiante::where('cedula', $cedula)->first();
            if($estudianteBuscado) {
                $userAuth = auth()->user();
                $querySeguimiento = \App\Models\Seguimiento::with(['horario'])->where('estudiante_id', $estudianteBuscado->id);
                if ($userAuth->rol !== 'admin') {
                    $querySeguimiento->whereHas('horario', function($q) use ($userAuth) {
                        $q->where('user_id', $userAuth->id);
                    });
                }
                $historialBuscado = $querySeguimiento->orderBy('fecha', 'desc')->get();
            } else {
                $errorBusqueda = "No se encontró ningún estudiante registrado con la cédula " . $cedula;
            }
        }
    @endphp

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
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>
                <a href="{{ route('seguimiento.index') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <span class="text-lg">🔎</span>
                    <span>Seguimiento</span>
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
                <button type="button" onclick="confirmarAccion(this, '¿Cerrar Sesión?', 'Saldrás del panel de gestión.', 'Sí, salir', '#002845')" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex justify-between items-center z-10 sticky top-0">
            <div>
                <h2 class="text-2xl font-black text-[#002845] flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Centro de Control
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Gestión de cursos y opciones de administración.</p>
            </div>
            @if(session('exito'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold border border-green-300 flex items-center">
                    ✅ {{ session('exito') }}
                </div>
            @endif
        </header>

        <div class="p-6 md:p-8 flex-1 bg-slate-50">
            <div class="max-w-7xl mx-auto">

                @if(session('error') || session('lista_errores'))
                    <div x-data="{ show: true }" x-show="show" class="mb-6 bg-white border border-red-200 rounded-2xl shadow overflow-hidden">
                        <div class="bg-red-50 border-b border-red-100 px-6 py-4 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl">⚠️</span>
                                <h3 class="text-red-800 font-black">Reporte del Sistema</h3>
                            </div>
                            <button @click="show = false" class="text-red-500 hover:text-red-800 bg-red-100 hover:bg-red-200 rounded-full w-8 h-8 flex items-center justify-center font-black">✖</button>
                        </div>
                        <div class="px-6 py-4">
                            @if(session('error'))
                                <p class="text-red-700 font-bold text-sm mb-3">{{ session('error') }}</p>
                            @endif
                            @if(session('lista_errores'))
                                <ul class="list-disc pl-5 text-xs text-red-600 font-semibold space-y-1 max-h-48 overflow-y-auto">
                                    @foreach(session('lista_errores') as $detalle)
                                        <li>{{ $detalle }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                <div x-data="{ tab: '{{ request('cedula') ? 'seguimiento' : 'clases' }}', search: '', cargando: false }">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-2 border-gray-200 mb-8 pb-4 gap-4">
                        <div class="flex space-x-2 overflow-x-auto w-full md:w-auto">
                            <button @click="tab = 'clases'" :class="tab === 'clases' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap border border-transparent flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Mis cursos
                            </button>
                            <button @click="tab = 'seguimiento'" :class="tab === 'seguimiento' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap border border-transparent flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Seguimiento estudiantil
                            </button>
                        </div>
                    </div>

                    <div x-show="tab === 'clases'">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($horarios as $clase)
                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden flex flex-col transform transition hover:scale-[1.02]">
                                    <div class="bg-gradient-to-r from-[#002845] to-blue-800 p-5 text-white relative">
                                        <h3 class="text-lg font-black truncate">{{ ucwords(strtolower($clase->curso_nombre)) }}</h3>
                                        <span class="text-[10px] bg-blue-500 px-2 py-1 rounded-full font-bold uppercase">{{ $clase->modalidad }}</span>
                                    </div>
                                    <div class="p-6 flex-1 flex flex-col">
                                        <div class="mb-4 text-sm text-gray-600 space-y-1">
                                            <p><span class="font-bold text-gray-800">Docente:</span> {{ $clase->docente_nombre }}</p>
                                            <p><span class="font-bold text-gray-800">Día:</span> {{ $clase->dia_semana }}</p>
                                            <p><span class="font-bold text-gray-800">Hora:</span> {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}</p>
                                        </div>
                                        <a href="{{ route('horarios.estudiantes', $clase->id) }}" class="mt-auto bg-[#002845] text-center text-white font-bold py-2 rounded-xl hover:bg-blue-900 transition">📋 Ver Estudiantes</a>
                                    </div>
                                </div>
                            @empty
                                <p class="col-span-full text-center py-10 font-bold text-gray-400">No hay cursos asignados.</p>
                            @endforelse
                        </div>
                    </div>

                    <div x-show="tab === 'seguimiento'" style="display: none;">

                        {{-- Mis materias --}}
                        @if($horarios->count() > 0)
                        <div class="mb-8">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Mis materias — ver inscritos por curso</p>
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
                                        <span class="bg-white/20 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $clase->seguimientos_count }}</span>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Búsqueda por cédula --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Buscar estudiante por cédula</p>
                            <form action="{{ route('dashboard') }}" method="GET" class="flex gap-3">
                                <input type="text" name="cedula" value="{{ request('cedula') }}" placeholder="Ej: 1001234567" required
                                       class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-[#002845] font-bold text-sm transition">
                                <button type="submit" class="bg-[#C9A227] text-[#002845] px-6 py-3 rounded-xl font-black hover:bg-yellow-500 transition text-sm">
                                    Buscar
                                </button>
                            </form>

                            @if($errorBusqueda)
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm font-medium">
                                {{ $errorBusqueda }}
                            </div>
                            @endif
                        </div>

                        {{-- Resultados --}}
                        @if(isset($estudianteBuscado))
                        <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="bg-[#002845] p-5 text-white flex items-center gap-4">
                                <div class="w-12 h-12 bg-[#C9A227] text-[#002845] rounded-xl flex items-center justify-center text-xl font-black flex-shrink-0">
                                    {{ strtoupper(substr($estudianteBuscado->nombre_completo, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-black text-lg truncate">{{ $estudianteBuscado->nombre_completo }}</div>
                                    <div class="text-blue-300 text-sm">C.C. {{ $estudianteBuscado->cedula }}</div>
                                </div>
                                <div class="text-center flex-shrink-0">
                                    <div class="text-3xl font-black text-[#C9A227]">{{ $historialBuscado->count() }}</div>
                                    <div class="text-xs text-blue-300 uppercase tracking-wide">asesorías</div>
                                </div>
                            </div>

                            @if($historialBuscado->isEmpty())
                                <div class="p-8 text-center text-gray-400 text-sm font-medium">
                                    Este estudiante no tiene asesorías registradas en ninguno de tus cursos.
                                </div>
                            @else
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Materia / Fecha</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase tracking-wide">Estado</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wide">Reporte de evolución</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($historialBuscado as $registro)
                                        <tr class="hover:bg-gray-50/80 transition">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-[#002845] text-sm">{{ ucwords(strtolower($registro->horario->curso_nombre ?? 'Clase eliminada')) }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                                @if($registro->estado === 'Evaluada')
                                                    @if($registro->asistencia)
                                                        <span class="bg-green-50 text-green-700 text-xs px-3 py-1 rounded-full font-bold border border-green-200">Asistió</span>
                                                    @else
                                                        <span class="bg-red-50 text-red-700 text-xs px-3 py-1 rounded-full font-bold border border-red-200">Faltó</span>
                                                    @endif
                                                @else
                                                    <span class="bg-yellow-50 text-yellow-700 text-xs px-3 py-1 rounded-full font-bold border border-yellow-200">Pendiente</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 italic max-w-xs">
                                                {{ $registro->evolucion ?? '—' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        @endif

                    </div>

                    @if(auth()->user()->rol === 'admin')
                    <div x-show="tab === 'usuarios'" style="display: none;">
                        <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden mb-8">
                            <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                                <h3 class="text-lg font-black text-purple-900">Administradores</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($usuarios->where('rol', 'admin') as $admin)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-[#002845] text-white flex items-center justify-center font-bold mr-4">{{ strtoupper(substr($admin->name, 0, 1)) }}</div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-900">{{ $admin->name }} @if(auth()->id() === $admin->id) (TÚ) @endif</p>
                                                        <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    @if($usuarios->where('rol', 'admin')->count() > 1)
                                                        <form action="{{ route('usuarios.actualizarRol', $admin->id) }}" method="POST" class="inline">
                                                            @csrf @method('PUT')
                                                            <button type="submit" name="rol" value="profesor" class="text-xs font-bold text-blue-600 hover:underline">Hacer Docente</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden mb-8">
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100">
                                <h3 class="text-lg font-black text-blue-900">Docentes</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($usuarios->where('rol', 'profesor') as $docente)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 flex items-center">
                                                    <div class="w-10 h-10 rounded-full bg-[#002845] flex items-center justify-center font-bold mr-4 text-sm" style="color:#C9A227">{{ strtoupper(substr($docente->name, 0, 1)) }}</div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-900">{{ $docente->name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $docente->email }}</p>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right flex justify-end gap-4">
                                                    <form action="{{ route('usuarios.actualizarRol', $docente->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <button type="submit" name="rol" value="admin" class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-xs font-bold hover:bg-purple-200">Hacer Admin</button>
                                                    </form>
                                                    <form action="{{ route('usuarios.eliminar', $docente->id) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="button" onclick="confirmarAccion(this, '¿Desactivar Docente?', 'Perderá el acceso al panel.', 'Sí, desactivar', '#dc2626')" class="text-red-600 text-xs font-bold hover:underline">Desactivar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-gray-100 rounded-3xl shadow-inner border border-gray-300 overflow-hidden opacity-75">
                            <div class="bg-gray-200 px-6 py-4 border-b border-gray-300">
                                <h3 class="text-lg font-black text-gray-600">Usuarios inactivos</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-gray-50 divide-y divide-gray-100">
                                        @foreach ($usuarios->where('rol', 'inactivo') as $inactivo)
                                            <tr class="hover:bg-gray-100 transition">
                                                <td class="px-6 py-4">
                                                    <p class="text-sm font-bold text-gray-500 line-through">{{ $inactivo->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $inactivo->email }}</p>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <form action="{{ route('usuarios.reactivar', $inactivo->id) }}" method="POST">
                                                        @csrf @method('PUT')
                                                        <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded-lg text-xs font-bold hover:bg-green-700 shadow-md">Reactivar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </main>

    <script>
        function confirmarAccion(buttonElement, titulo, texto, textoBotonConfirmar, colorBoton) {
            Swal.fire({
                title: titulo, text: texto, icon: 'warning', showCancelButton: true,
                confirmButtonColor: colorBoton, cancelButtonColor: '#9ca3af',
                confirmButtonText: textoBotonConfirmar, cancelButtonText: 'Cancelar',
                customClass: { title: 'font-extrabold text-[#002845]', popup: 'rounded-3xl shadow-2xl border border-gray-100' }
            }).then((result) => { if (result.isConfirmed) { buttonElement.closest('form').submit(); } })
        }
    </script>
</body>
</html>