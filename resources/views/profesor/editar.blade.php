<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Clase - Agenda U</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-30 md:hidden" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="w-64 bg-[#002845] text-white flex flex-col fixed md:relative inset-y-0 left-0 z-40 shadow-2xl transition-transform duration-300 md:translate-x-0 flex-shrink-0">
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
                <div class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center text-white text-sm font-bold shadow-inner uppercase">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                        {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}
                    </p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>

                @if(auth()->user()->rol === 'admin')
                    <a href="{{ route('admin.encuestas') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        <span>Satisfacción</span>
                    </a>
                @else
                    <a href="{{ route('seguimiento.index') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                        <span class="text-lg">🔎</span>
                        <span>Seguimiento</span>
                    </a>
                @endif

                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Calendario Público</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white p-4 md:p-6 shadow-sm border-b border-gray-200 flex items-center gap-3 sticky top-0 z-10">
            <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-[#002845] hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h2 class="text-xl md:text-2xl font-black text-[#002845]">Editar Clase</h2>
                <p class="text-gray-500 text-xs md:text-sm mt-0.5 font-medium">{{ $horario->curso_nombre }}</p>
            </div>
        </header>

        <div class="p-4 md:p-8 flex-1">
            <div class="max-w-3xl mx-auto">

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
                        <p class="font-bold text-red-700 mb-2 text-sm">Corrige los siguientes errores:</p>
                        <ul class="list-disc pl-5 text-xs text-red-600 space-y-1">
                            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">

                    <div class="bg-gradient-to-r from-[#002845] to-[#004273] px-6 md:px-8 py-5 md:py-6 text-white">
                        <h3 class="text-xl md:text-2xl font-black tracking-wide">Modificar Detalles del Horario</h3>
                        <p class="text-blue-200 text-sm mt-1">Todos los campos marcados con * son obligatorios.</p>
                    </div>

                    <form action="{{ route('horarios.actualizar', $horario->id) }}" method="POST" class="p-6 md:p-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Nombre del Curso *</label>
                                <input type="text" name="curso_nombre" value="{{ old('curso_nombre', $horario->curso_nombre) }}" required
                                       class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] focus:bg-white outline-none transition">
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Asignar Docente *</label>
                                <select name="user_id" class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] focus:bg-white outline-none transition">
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->id }}" {{ $horario->user_id == $profesor->id ? 'selected' : '' }}>
                                            {{ $profesor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="docente_nombre" value="{{ $horario->docente_nombre }}">

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Día de la Semana *</label>
                                <select name="dia_semana" class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] focus:bg-white outline-none transition">
                                    @foreach(['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'] as $dia)
                                        <option value="{{ $dia }}" {{ $horario->dia_semana === $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Hora de Inicio *</label>
                                <input type="time" name="hora_inicio" value="{{ $horario->hora_inicio }}" required
                                       class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Hora de Fin *</label>
                                <input type="time" name="hora_fin" value="{{ $horario->hora_fin }}" required
                                       class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition">
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6 mb-6">
                            <h4 class="text-base font-black text-[#002845] mb-4">Ubicación</h4>

                            <div class="mb-4">
                                <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Modalidad</label>
                                <select name="modalidad" id="sel_modalidad"
                                        class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition"
                                        onchange="toggleUbicacion(this.value)">
                                    <option value="Presencial" {{ strtolower(trim($horario->modalidad)) !== 'virtual' ? 'selected' : '' }}>Presencial</option>
                                    <option value="Virtual" {{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>

                            <div id="campos_fisicos" class="grid grid-cols-1 sm:grid-cols-3 gap-5" style="{{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'display:none;' : '' }}">
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Sede</label>
                                    <input type="text" name="sede" value="{{ old('sede', $horario->sede) }}" placeholder="Ej: Robledo"
                                           class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Bloque</label>
                                    <input type="text" name="bloque" value="{{ old('bloque', $horario->bloque) }}" placeholder="Ej: 6"
                                           class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1.5">Aula</label>
                                    <input type="text" name="aula" value="{{ old('aula', $horario->aula) }}" placeholder="Ej: 401"
                                           class="block w-full rounded-xl border-2 border-gray-200 bg-gray-50 p-3 text-sm font-bold focus:border-[#002845] outline-none transition">
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-100">
                            <a href="{{ route('dashboard') }}"
                               class="text-center bg-white text-gray-700 border-2 border-gray-200 px-6 py-3 rounded-xl font-bold hover:bg-gray-50 transition text-sm">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="bg-[#002845] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg transition text-sm">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function toggleUbicacion(val) {
            document.getElementById('campos_fisicos').style.display = val === 'Virtual' ? 'none' : 'grid';
        }
    </script>
</body>
</html>
