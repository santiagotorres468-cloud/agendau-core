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

    <aside class="w-64 bg-[#002845] text-white flex flex-col hidden md:flex shadow-2xl z-20">
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
                    <p class="text-xs text-blue-200 flex items-center font-medium"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>
                <a href="{{ route('admin.encuestas') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
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
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 10000)" x-show="show" x-transition.duration.500ms class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold border border-green-300 shadow-sm flex items-center">
                    <span class="mr-2">✅</span> {{ session('exito') }}
                </div>
            @endif
        </header>

        <div class="p-6 md:p-8 flex-1 bg-slate-50">
            <div class="max-w-7xl mx-auto">

                @if(session('error') || session('lista_errores') || $errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition class="mb-8 bg-white border border-red-200 rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-red-50 border-b border-red-100 px-6 py-4 flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <span class="text-xl">⚠️</span>
                                <div>
                                    <h3 class="text-red-800 font-black text-base leading-tight">No se pudo completar la operación</h3>
                                    <p class="text-red-400 text-xs font-medium mt-0.5">Revisa los detalles a continuación</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-red-400 hover:text-red-700 bg-red-100 hover:bg-red-200 rounded-full w-8 h-8 flex items-center justify-center transition font-black text-sm">✕</button>
                        </div>

                        <div class="px-6 py-5 space-y-4">
                            @if(session('error'))
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </span>
                                    <p class="text-red-700 font-semibold text-sm leading-snug">{{ session('error') }}</p>
                                </div>
                            @endif

                            @if(session('lista_errores') || $errors->any())
                                <div class="bg-red-50 rounded-xl p-4 border border-red-100 max-h-52 overflow-y-auto">
                                    <p class="text-xs font-bold text-red-400 uppercase tracking-wider mb-2">Detalles</p>
                                    <ul class="space-y-2">
                                        @foreach(array_merge(session('lista_errores') ?? [], $errors->all()) as $detalle)
                                            <li class="flex items-start gap-2 text-sm text-red-700">
                                                <span class="mt-1 w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                                {{ $detalle }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                @php $activeTab = request()->query('tab', 'clases'); @endphp
                <div x-data="{ tab: '{{ $activeTab }}', search: '' }" class="w-full">
    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-2 border-gray-200 mb-8 pb-4 gap-4">
        
                        <div class="flex space-x-2 overflow-x-auto w-full md:w-auto">
                            <button @click="tab = 'clases'" :class="tab === 'clases' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer border border-transparent">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Gestión de cursos
                            </button>

                            @if(auth()->user()->rol === 'admin')
                                <button @click="tab = 'importar'" :class="tab === 'importar' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer border border-transparent">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Carga masiva
                                </button>
                                <button @click="tab = 'reportes'" :class="tab === 'reportes' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer border border-transparent">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Informes
                                </button>
                                <button @click="tab = 'usuarios'" :class="tab === 'usuarios' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-all whitespace-nowrap flex items-center gap-2 cursor-pointer border border-transparent">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    Control de roles
                                </button>
                            @endif
                        </div>

                        <div class="relative w-full md:w-80" x-show="tab === 'clases'">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="search" class="bg-white border-2 border-gray-200 text-gray-900 font-bold text-sm rounded-xl focus:ring-[#002845] focus:border-[#002845] block w-full pl-11 p-3 shadow-sm transition" placeholder="Buscar por docente o curso...">
                        </div>
                    </div>

                    <div x-show="tab === 'clases'" class="transition-all duration-300" style="{{ $activeTab !== 'clases' ? 'display:none' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($horarios as $clase)
                                <div x-show="search === '' || '{{ mb_strtolower(addslashes($clase->curso_nombre), 'UTF-8') }}'.includes(search.toLowerCase()) || '{{ mb_strtolower(addslashes($clase->docente_nombre), 'UTF-8') }}'.includes(search.toLowerCase())" 
                                     class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition duration-300 hover:scale-[1.02] hover:shadow-xl flex flex-col">
                                    
                                    <div class="bg-gradient-to-r from-[#002845] to-blue-800 p-5 relative">
                                        <h3 class="text-lg font-black text-white truncate pr-4" title="{{ $clase->curso_nombre }}">{{ ucwords(strtolower($clase->curso_nombre)) }}</h3>
                                        @if(strtolower(trim($clase->modalidad)) === 'virtual')
                                            <span class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-3xl shadow-sm uppercase tracking-wide">Virtual</span>
                                        @else
                                            <span class="absolute top-0 right-0 bg-[#C9A227] text-[#002845] text-[10px] font-bold px-3 py-1 rounded-bl-3xl shadow-sm uppercase tracking-wide">Presencial</span>
                                        @endif
                                    </div>
                                    
                                    <div class="p-6 flex-1 flex flex-col">
                                        <div class="mb-4 text-sm text-gray-600 space-y-2">
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">Docente:</span> {{ $clase->docente_nombre }}</p>
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">📅 Día:</span> <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-bold border border-blue-100">{{ $clase->dia_semana }}</span></p>
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">⏰ Hora:</span> {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}</p>
                                            
                                            <p class="flex items-center mt-3 pt-2 border-t border-gray-100">
                                                <span class="font-bold text-gray-800 w-20">👥 Inscritos:</span> 
                                                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-black border border-green-200">{{ $clase->seguimientos_count ?? 0 }} estudiantes</span>
                                            </p>
                                            @if(strtolower(trim($clase->modalidad)) !== 'virtual')
                                                <div class="mt-3 pt-3 border-t border-gray-100 bg-gray-50 rounded-lg p-3 text-xs">
                                                    <p><span class="font-bold text-gray-700">Sede:</span> {{ $clase->sede ?: 'N/A' }}</p>
                                                    <p><span class="font-bold text-gray-700">Bloque:</span> {{ $clase->bloque ?: '-' }} | <span class="font-bold text-gray-700">Aula:</span> {{ $clase->aula ?: '-' }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-auto grid grid-cols-2 gap-2 pt-4 border-t border-gray-100">
                                            <a href="{{ route('horarios.estudiantes', $clase->id) }}" class="col-span-2 bg-[#002845] text-center text-white font-semibold py-2 rounded-xl hover:bg-blue-900 transition shadow flex items-center justify-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                Ver estudiantes
                                            </a>
                                            @if(auth()->user()->rol === 'admin')
                                                <a href="{{ route('horarios.editar', $clase->id) }}" class="bg-[#FBF6E6] text-[#A87E1A] text-center font-semibold py-2 rounded-xl hover:bg-[#F5E9C7] transition text-sm flex items-center justify-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Editar
                                                </a>
                                                <form action="{{ route('horarios.eliminar', $clase->id) }}" method="POST" class="m-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmarAccion(this, '¿Eliminar este curso?', 'Se borrará permanentemente del sistema.', 'Sí, eliminar', '#dc2626')" class="w-full bg-red-50 text-red-600 text-center font-semibold py-2 rounded-xl hover:bg-red-100 transition text-sm flex items-center justify-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Borrar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full bg-white p-12 rounded-3xl shadow-sm text-center border border-dashed border-gray-300">
                                    <span class="text-6xl mb-4 block">📭</span>
                                    <h3 class="text-xl font-black text-gray-700">Aún no hay cursos asignados</h3>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if(auth()->user()->rol === 'admin')
                        <div x-show="tab === 'importar'" class="transition-all duration-300" style="{{ $activeTab !== 'importar' ? 'display:none' : '' }}">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden max-w-2xl mx-auto">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-8 py-6 text-white text-center">
                                    <span class="text-5xl block mb-2">📊</span>
                                    <h3 class="text-2xl font-extrabold tracking-wide">Subir horarios por archivo</h3>
                                    <p class="text-sm text-white/80 mt-1 font-medium">(Usa esta opción para cargar varios horarios al mismo tiempo mediante un archivo Excel o CSV)</p>
                                </div>
                                <form action="{{ route('horarios.importar') }}" method="POST" enctype="multipart/form-data" class="p-8">
                                @csrf
                                
                                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-xl shadow-sm">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="text-xl">⚠️</span>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-extrabold text-yellow-800 uppercase tracking-wide">Aviso de Responsabilidad</h3>
                                            <div class="mt-1 text-xs text-yellow-700 font-medium">
                                                La importación masiva afecta directamente la base de datos de <strong>Agenda U</strong>. Cualquier cambio, alteración o reemplazo de datos se realiza bajo la absoluta responsabilidad del administrador en turno.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl shadow-sm">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="text-xl">ℹ️</span>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-extrabold text-blue-800 uppercase tracking-wide">Formato Requerido</h3>
                                            <div class="mt-1 text-xs text-blue-700 font-medium">
                                                Para evitar errores, el archivo debe contener exactamente las siguientes columnas en la primera fila: <strong>curso, docente, dia, inicio, fin, semestre, lugar, modalidad, sede, bloque, aula</strong>.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-6 text-center">
                                    <a href="{{ route('horarios.plantilla') }}" class="text-sm text-blue-600 hover:text-blue-800 underline font-bold inline-flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Descargar plantilla de carga masiva (.xlsx)
                                    </a>
                                </div>

                                <div x-data="{ fileName: null }" class="mb-8">
                                    <label :class="fileName ? 'bg-emerald-50 border-emerald-400' : 'bg-white border-gray-300 hover:bg-gray-50'" class="flex flex-col justify-center items-center w-full h-32 px-4 transition-all duration-300 border-2 border-dashed rounded-xl appearance-none cursor-pointer shadow-sm">

                                        <div x-show="!fileName" class="flex flex-col items-center space-y-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                            <span class="font-bold text-gray-600">Haz clic para seleccionar tu archivo Excel</span>
                                        </div>

                                        <div x-show="fileName" style="display: none;" class="flex flex-col items-center space-y-1">
                                            <span class="text-4xl drop-shadow-sm mb-1">📄</span>
                                            <span class="font-extrabold text-emerald-800 text-lg truncate max-w-xs" x-text="fileName"></span>
                                            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest bg-emerald-100 px-3 py-1 rounded-full">¡Archivo listo para subir!</span>
                                        </div>

                                        <input type="file" name="archivo_excel" class="hidden" required accept=".xlsx, .xls" @change="fileName = $event.target.files[0].name" />
                                    </label>
                                </div>

                                <button type="submit" class="w-full bg-emerald-600 text-white font-extrabold py-4 rounded-xl shadow-lg hover:bg-emerald-700 transition transform hover:-translate-y-1 flex justify-center items-center normal-case">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Subir e importar datos
                                </button>
                                </form>
                                
                                <div class="mt-6 p-5 bg-red-50 border-t border-red-200 rounded-b-3xl">
                                    <h4 class="font-bold text-red-800 mb-1.5 flex items-center justify-center text-sm">
                                        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        Zona de peligro
                                    </h4>
                                    <p class="text-xs text-red-500 mb-4 text-center">Elimina todas las clases y reservas de forma permanente. No se puede deshacer.</p>
                                    <form action="{{ route('horarios.vaciar') }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmarAccion(this, '¿Vaciar todo el sistema?', 'Se eliminarán todas las clases y reservas de forma permanente.', 'Sí, vaciar todo', '#b91c1c')" class="w-full bg-red-600 text-white font-semibold py-2.5 rounded-xl hover:bg-red-700 transition shadow flex justify-center items-center gap-2 text-sm">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Vaciar todo el sistema
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'reportes'" class="transition-all duration-300" style="{{ $activeTab !== 'reportes' ? 'display:none' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                                
                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                                    <div class="bg-[#002845] px-6 py-4 text-white font-semibold flex items-center gap-2">
                                        <svg class="w-4 h-4 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Informe por docente
                                    </div>
                                    <form action="{{ route('reportes.docente') }}" method="GET" target="_blank" class="p-6">
                                        <p class="text-sm text-gray-500 mb-4">Genera un informe de las asesorías brindadas por un docente.</p>
                                        <select name="docente_id" required class="w-full mb-6 bg-gray-50 border-gray-300 rounded-xl focus:ring-[#002845] focus:border-[#002845] font-bold p-3">
                                            <option value="">Seleccione un docente...</option>
                                            @foreach($usuarios->where('rol', 'profesor') as $profe)
                                                <option value="{{ $profe->id }}">{{ $profe->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full bg-[#002845] text-white font-black py-3 rounded-xl hover:bg-blue-900 transition shadow-md">Generar PDF</button>
                                    </form>
                                </div>

                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                                    <div class="bg-emerald-600 px-6 py-4 text-white font-semibold flex items-center gap-2">
                                        <svg class="w-4 h-4 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        Informe por curso
                                    </div>
                                    <form action="{{ route('reportes.curso') }}" method="GET" target="_blank" class="p-6">
                                        <p class="text-sm text-gray-500 mb-4">Genera un reporte global del impacto de un curso.</p>
                                        <select name="curso_nombre" required class="w-full mb-6 bg-gray-50 border-gray-300 rounded-xl focus:ring-emerald-600 focus:border-emerald-600 font-bold p-3">
                                            <option value="">Seleccione un curso...</option>
                                            @foreach($horarios->unique('curso_nombre') as $cursoUnico)
                                                <option value="{{ $cursoUnico->curso_nombre }}">{{ $cursoUnico->curso_nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full bg-emerald-600 text-white font-black py-3 rounded-xl hover:bg-emerald-700 transition shadow-md">Generar PDF</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div x-show="tab === 'usuarios'" class="transition-all duration-300" style="{{ $activeTab !== 'usuarios' ? 'display:none' : '' }}">
                            
                            <div class="mb-10 bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                                <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                                    <h3 class="text-xl font-extrabold text-purple-900 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Administradores
                                    </h3>
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest">Seguridad</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            
                                            @php $totalAdmins = $usuarios->where('rol', 'admin')->count(); @endphp

                                            @foreach ($usuarios->where('rol', 'admin') as $admin)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 bg-[#002845] rounded-full flex items-center justify-center text-white font-bold shadow-inner">
                                                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-bold text-gray-900">
                                                                    {{ $admin->name }} 
                                                                    @if(auth()->id() === $admin->id) <span class="text-blue-500 font-extrabold text-xs ml-1">(TÚ)</span> @endif
                                                                </div>
                                                                <div class="text-xs text-gray-500">{{ $admin->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <div class="flex items-center justify-end space-x-3 pr-4">
                                                            
                                                            @if($totalAdmins <= 1)
                                                                <span class="bg-gray-50 text-gray-400 font-semibold py-2 px-5 rounded-lg border border-gray-200 text-xs flex items-center gap-1.5 cursor-not-allowed">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                                    Único administrador
                                                                </span>
                                                            @else
                                                                <form action="{{ route('usuarios.actualizarRol', $admin->id) }}" method="POST" class="flex items-center space-x-2">
                                                                    @csrf @method('PUT')
                                                                    <select name="rol" class="text-sm border-gray-300 rounded-lg bg-gray-50 font-bold focus:ring-[#002845] focus:border-[#002845]">
                                                                        <option value="admin" selected>Admin</option>
                                                                        <option value="profesor">Docente</option>
                                                                    </select>
                                                                    <button type="submit" class="bg-[#C9A227] hover:bg-[#A87E1A] text-[#002845] font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">Actualizar</button>
                                                                </form>
                                                                
                                                                <form action="{{ route('usuarios.eliminar', $admin->id) }}" method="POST" class="m-0">
                                                                    @csrf @method('DELETE')
                                                                    <button type="button" onclick="confirmarAccion(this, '¿Desactivar administrador?', 'Esta cuenta pasará a estado inactivo.', 'Sí, desactivar', '#dc2626')" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 w-32 text-center rounded-lg transition text-xs shadow-md flex items-center justify-center gap-1">
                                                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                                        Desactivar
                                                                    </button>
                                                                </form>
                                                            @endif

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden mb-10">
                                <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-start items-center">
                                    <h3 class="text-xl font-extrabold text-[#002845] flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        Docentes
                                    </h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach ($usuarios->where('rol', 'profesor') as $docente)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 bg-[#002845] rounded-full flex items-center justify-center font-bold shadow-inner text-sm" style="color:#C9A227">
                                                                {{ strtoupper(substr($docente->name, 0, 1)) }}
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-bold text-gray-900">{{ $docente->name }}</div>
                                                                <div class="text-xs text-gray-500">{{ $docente->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <div class="flex items-center justify-end space-x-3 pr-4">
                                                            <form action="{{ route('usuarios.actualizarRol', $docente->id) }}" method="POST" class="flex items-center space-x-2">
                                                                @csrf @method('PUT')
                                                                <select name="rol" class="text-sm border-gray-300 rounded-lg bg-gray-50 font-bold focus:ring-[#002845] focus:border-[#002845]">
                                                                    <option value="profesor" selected>Docente</option>
                                                                    <option value="admin">Admin</option>
                                                                </select>
                                                                <button type="submit" class="bg-[#C9A227] hover:bg-[#A87E1A] text-[#002845] font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">Actualizar</button>
                                                            </form>

                                                            <form action="{{ route('usuarios.eliminar', $docente->id) }}" method="POST" class="m-0">
                                                                @csrf @method('DELETE')
                                                                <button type="button" onclick="confirmarAccion(this, '¿Desactivar a este docente?', 'Su cuenta pasará a estado inactivo.', 'Sí, desactivar', '#dc2626')" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 w-32 text-center rounded-lg transition text-xs shadow-md flex items-center justify-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                                    Desactivar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($usuarios->where('rol', 'profesor')->isEmpty())
                                                <tr><td colspan="2" class="px-6 py-8 text-center text-sm font-bold text-gray-400">No hay docentes registrados.</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-gray-100 rounded-3xl shadow-inner border border-gray-300 overflow-hidden opacity-90">
                                <div class="bg-gray-200 px-6 py-4 border-b border-gray-300 flex justify-start items-center">
                                    <h3 class="text-xl font-extrabold text-gray-600 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                        Usuarios inactivos
                                    </h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <tbody class="bg-gray-50 divide-y divide-gray-200">
                                            @foreach ($usuarios->where('rol', 'inactivo') as $inactivo)
                                                <tr class="hover:bg-gray-100 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-400 rounded-full flex items-center justify-center text-white font-bold shadow-inner">
                                                                {{ strtoupper(substr($inactivo->name, 0, 1)) }}
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-bold text-gray-500 line-through">{{ $inactivo->name }}</div>
                                                                <div class="text-xs text-gray-400">{{ $inactivo->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <div class="flex items-center justify-end space-x-3 pr-4">
                                                            <form action="{{ route('usuarios.reactivar', $inactivo->id) }}" method="POST" class="flex items-center space-x-2 m-0">
                                                                @csrf @method('PUT')
                                                                <select name="rol" class="text-sm border-gray-300 rounded-lg bg-white font-bold focus:ring-green-500 focus:border-green-500">
                                                                    <option value="profesor" selected>Como Docente</option>
                                                                    <option value="admin">Como Admin</option>
                                                                </select>
                                                                <button type="button" onclick="confirmarAccion(this, '¿Reactivar usuario?', 'Volverá a tener acceso al sistema.', 'Sí, reactivar', '#10b981')" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-5 rounded-lg transition text-xs shadow-md flex items-center gap-1">
                                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                    Reactivar
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($usuarios->where('rol', 'inactivo')->isEmpty())
                                                <tr><td colspan="2" class="px-6 py-8 text-center text-sm font-bold text-gray-400">No hay usuarios inactivos.</td></tr>
                                            @endif
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

    {{-- ── MODAL RESULTADO DE IMPORTACIÓN ──────────────────────────────── --}}
    @if(session('import_popup'))
    @php
        $imp_insertados   = session('import_insertados', 0);
        $imp_fueraRango   = session('import_fuera_rango', []);
        $imp_conflictos   = session('import_conflictos', []);
        $imp_otros        = session('import_otros', []);
        $imp_totalOmitidos = count($imp_fueraRango) + count($imp_conflictos) + count($imp_otros);
    @endphp
    <div id="modal-importacion" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.55);" onclick="if(event.target===this)cerrarModalImport()">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 w-full max-w-lg max-h-[90vh] flex flex-col" onclick="event.stopPropagation()">

            {{-- Cabecera --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#002845] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-[#002845] text-base leading-tight">Resultado de importación</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Resumen del archivo procesado</p>
                    </div>
                </div>
                <button onclick="cerrarModalImport()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition text-gray-500 hover:text-gray-700 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Contadores --}}
            <div class="grid grid-cols-2 gap-4 px-6 py-5 border-b border-gray-100">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-4 text-center">
                    <p class="text-3xl font-black text-emerald-700">{{ $imp_insertados }}</p>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wide mt-1">Registros guardados</p>
                </div>
                <div class="bg-{{ $imp_totalOmitidos > 0 ? 'amber' : 'gray' }}-50 border border-{{ $imp_totalOmitidos > 0 ? 'amber' : 'gray' }}-200 rounded-2xl px-4 py-4 text-center">
                    <p class="text-3xl font-black text-{{ $imp_totalOmitidos > 0 ? 'amber-700' : 'gray-400' }}">{{ $imp_totalOmitidos }}</p>
                    <p class="text-xs font-bold text-{{ $imp_totalOmitidos > 0 ? 'amber-600' : 'gray-400' }} uppercase tracking-wide mt-1">Omitidos</p>
                </div>
            </div>

            {{-- Detalle de fallos --}}
            @if($imp_totalOmitidos > 0)
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">

                @if(!empty($imp_fueraRango))
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-orange-500 flex-shrink-0"></span>
                        <p class="text-xs font-black text-orange-600 uppercase tracking-wider">Fuera de horario institucional ({{ count($imp_fueraRango) }})</p>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach($imp_fueraRango as $msg)
                        <li class="flex items-start gap-2 bg-orange-50 border border-orange-100 rounded-xl px-3 py-2">
                            <svg class="w-3.5 h-3.5 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs text-orange-800 leading-snug">{{ $msg }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(!empty($imp_conflictos))
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
                        <p class="text-xs font-black text-red-600 uppercase tracking-wider">Conflictos de horario ({{ count($imp_conflictos) }})</p>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach($imp_conflictos as $msg)
                        <li class="flex items-start gap-2 bg-red-50 border border-red-100 rounded-xl px-3 py-2">
                            <svg class="w-3.5 h-3.5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            <span class="text-xs text-red-800 leading-snug">{{ $msg }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(!empty($imp_otros))
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-400 flex-shrink-0"></span>
                        <p class="text-xs font-black text-gray-500 uppercase tracking-wider">Otros ({{ count($imp_otros) }})</p>
                    </div>
                    <ul class="space-y-1.5">
                        @foreach($imp_otros as $msg)
                        <li class="flex items-start gap-2 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2">
                            <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs text-gray-700 leading-snug">{{ $msg }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
            @else
            <div class="px-6 py-6 text-center">
                <svg class="w-10 h-10 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-bold text-emerald-700">Todos los registros fueron guardados exitosamente.</p>
            </div>
            @endif

            {{-- Pie --}}
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end">
                <button onclick="cerrarModalImport()" class="bg-[#002845] text-white font-bold px-6 py-2.5 rounded-xl hover:bg-[#003A5C] transition text-sm">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        function cerrarModalImport() {
            const m = document.getElementById('modal-importacion');
            if (m) m.style.display = 'none';
        }

        function confirmarAccion(buttonElement, titulo, texto, textoBotonConfirmar, colorBoton) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: colorBoton,
                cancelButtonColor: '#9ca3af',
                confirmButtonText: textoBotonConfirmar,
                cancelButtonText: 'Cancelar',
                customClass: {
                    title: 'font-extrabold text-[#002845]',
                    popup: 'rounded-3xl shadow-2xl border border-gray-100',
                    confirmButton: 'font-bold px-6 py-2.5 rounded-xl text-sm',
                    cancelButton: 'font-bold px-6 py-2.5 rounded-xl text-sm text-gray-700 bg-gray-200 hover:bg-gray-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    buttonElement.closest('form').submit();
                }
            })
        }
    </script>
</body>
</html>