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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col hidden md:flex shadow-2xl z-20">
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <span class="text-3xl">🎓</span>
            <h1 class="text-2xl font-black tracking-wide">Agenda U</h1>
        </div>
        
        <div class="p-6">
            <p class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-2">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-xl font-bold shadow-inner uppercase">
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
                                <span class="text-2xl">⚠️</span>
                                <h3 class="text-red-800 font-black text-lg">Reporte del Sistema</h3>
                            </div>
                            <button @click="show = false" class="text-red-500 hover:text-red-800 bg-red-100 hover:bg-red-200 rounded-full w-8 h-8 flex items-center justify-center transition font-black">✖</button>
                        </div>
                        
                        <div class="px-6 py-5">
                            @if(session('error'))
                                <p class="text-red-700 font-bold text-sm mb-3">{{ session('error') }}</p>
                            @endif

                            @if(session('lista_errores') || $errors->any())
                                <div class="bg-red-50/50 rounded-xl p-4 max-h-48 overflow-y-auto border border-red-100 shadow-inner">
                                    <ul class="list-disc pl-5 text-xs text-red-600 font-semibold space-y-1.5">
                                        @if(session('lista_errores'))
                                            @foreach(session('lista_errores') as $detalle)
                                                <li>{{ $detalle }}</li>
                                            @endforeach
                                        @endif
                                        @if($errors->any())
                                            @foreach($errors->all() as $detalle)
                                                <li>{{ $detalle }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <div x-data="{ tab: 'clases', search: '' }" class="w-full">
    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b-2 border-gray-200 mb-8 pb-4 gap-4">
        
                        <div class="flex space-x-2 overflow-x-auto w-full md:w-auto">
                            <button @click="tab = 'clases'" :class="tab === 'clases' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-bold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                📚 Gestión de Cursos
                            </button>
            
                            @if(auth()->user()->rol === 'admin')
                                <button @click="tab = 'importar'" :class="tab === 'importar' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-bold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                    📤 Importar Excel
                                </button>
                                <button @click="tab = 'reportes'" :class="tab === 'reportes' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-bold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                    📊 Informes
                                </button>
                                <button @click="tab = 'usuarios'" :class="tab === 'usuarios' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-bold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                    👥 Control de Roles
                                </button>
                            @endif
                        </div>

                        <div class="relative w-full md:w-80" x-show="tab === 'clases'">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" x-model="search" class="bg-white border-2 border-gray-200 text-gray-900 font-bold text-sm rounded-xl focus:ring-[#002845] focus:border-[#002845] block w-full pl-11 p-3 shadow-sm transition" placeholder="Buscar por profesor o curso...">
                        </div>
                    </div>

                    <div x-show="tab === 'clases'" class="transition-all duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($horarios as $clase)
                                <div x-show="search === '' || '{{ mb_strtolower(addslashes($clase->curso_nombre), 'UTF-8') }}'.includes(search.toLowerCase()) || '{{ mb_strtolower(addslashes($clase->docente_nombre), 'UTF-8') }}'.includes(search.toLowerCase())" 
                                     class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition duration-300 hover:scale-[1.02] hover:shadow-xl flex flex-col">
                                    
                                    <div class="bg-gradient-to-r from-[#002845] to-blue-800 p-5 relative">
                                        <h3 class="text-lg font-black text-white truncate pr-4" title="{{ $clase->curso_nombre }}">{{ $clase->curso_nombre }}</h3>
                                        @if(strtolower(trim($clase->modalidad)) === 'virtual')
                                            <span class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-3xl shadow-sm uppercase tracking-wide">Virtual</span>
                                        @else
                                            <span class="absolute top-0 right-0 bg-[#FFD700] text-[#002845] text-[10px] font-bold px-3 py-1 rounded-bl-3xl shadow-sm uppercase tracking-wide">Presencial</span>
                                        @endif
                                    </div>
                                    
                                    <div class="p-6 flex-1 flex flex-col">
                                        <div class="mb-4 text-sm text-gray-600 space-y-2">
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">👨‍🏫 Docente:</span> {{ $clase->docente_nombre }}</p>
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
                                            <a href="{{ route('horarios.estudiantes', $clase->id) }}" class="col-span-2 bg-[#002845] text-center text-white font-bold py-2 rounded-xl hover:bg-blue-900 transition shadow">
                                                📋 Ver Estudiantes
                                            </a>
                                            @if(auth()->user()->rol === 'admin')
                                                <a href="{{ route('horarios.editar', $clase->id) }}" class="bg-yellow-100 text-yellow-800 text-center font-bold py-2 rounded-xl hover:bg-yellow-200 transition text-sm flex items-center justify-center">
                                                    ✏️ Editar
                                                </a>
                                                <form action="{{ route('horarios.eliminar', $clase->id) }}" method="POST" class="m-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmarAccion(this, '¿Eliminar este curso?', 'Se borrará el curso del sistema.', 'Sí, eliminar', '#dc2626')" class="w-full bg-red-100 text-red-700 text-center font-bold py-2 rounded-xl hover:bg-red-200 transition text-sm flex items-center justify-center">
                                                        🗑️ Borrar
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
                        <div style="display: none;" x-show="tab === 'importar'" class="transition-all duration-300">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden max-w-2xl mx-auto">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-8 py-6 text-white text-center">
                                    <span class="text-5xl block mb-2">📊</span>
                                    <h3 class="text-2xl font-extrabold tracking-wide">Carga Masiva de Horarios</h3>
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

                                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-xl shadow-sm">
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
                                    
                                    <div class="mt-4 text-center">
                                        <a href="{{ asset('plantillas/plantilla_horarios.xlsx') }}" download class="text-sm text-blue-600 hover:text-blue-800 underline font-bold flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Descargar plantilla para realizar carga masiva con la plantilla de la programación
                                        </a>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-emerald-600 text-white font-extrabold py-4 rounded-xl shadow-lg hover:bg-emerald-700 transition transform hover:-translate-y-1 flex justify-center items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Subir e Importar Datos
                                </button>
                                </form>
                                
                                <div class="mt-6 p-6 bg-red-50 border-t-2 border-red-200">
                                    <h4 class="font-black text-red-800 mb-2 flex items-center justify-center"><span class="mr-2">🚨</span> Zona de Peligro</h4>
                                    <p class="text-xs text-red-600 mb-4 text-center">Esta acción eliminará todas las clases y reservas de la base de datos de manera irreversible.</p>
                                    <form action="{{ route('horarios.vaciar') }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmarAccion(this, '¿VACIAR TODA LA BASE DE DATOS?', 'ESTO BORRARÁ TODAS LAS CLASES Y RESERVAS. NO SE PUEDE DESHACER.', 'SÍ, BORRAR TODO', '#b91c1c')" class="w-full bg-red-600 text-white font-black py-3 rounded-xl hover:bg-red-800 transition shadow-md flex justify-center items-center">
                                            🗑️ VACIAR TODAS LAS CLASES
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div style="display: none;" x-show="tab === 'reportes'" class="transition-all duration-300">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-5xl mx-auto">
                                
                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                                    <div class="bg-[#002845] px-6 py-4 text-white font-bold flex items-center">
                                        <span class="text-2xl mr-2">👨‍🏫</span> Informe por Docente
                                    </div>
                                    <form action="{{ route('reportes.docente') }}" method="GET" target="_blank" class="p-6">
                                        <p class="text-sm text-gray-500 mb-4">Genera un informe de las asesorías brindadas por un profesor.</p>
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
                                    <div class="bg-emerald-600 px-6 py-4 text-white font-bold flex items-center">
                                        <span class="text-2xl mr-2">📚</span> Informe por Curso
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

                        <div style="display: none;" x-show="tab === 'usuarios'" class="transition-all duration-300">
                            
                            <div class="mb-10 bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                                <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                                    <h3 class="text-xl font-extrabold text-purple-900 flex items-center">👑 Administradores</h3>
                                    <span class="bg-purple-100 text-purple-800 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-widest">Seguridad Nivel 1</span>
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
                                                                <button disabled class="bg-red-50 text-red-500 font-extrabold py-2 px-5 rounded-lg border border-red-200 text-xs flex items-center cursor-not-allowed shadow-inner opacity-90">
                                                                    🛡️ ÚNICO ADMIN (INBORRABLE)
                                                                </button>
                                                            @else
                                                                <form action="{{ route('usuarios.actualizarRol', $admin->id) }}" method="POST" class="flex items-center space-x-2">
                                                                    @csrf @method('PUT')
                                                                    <select name="rol" class="text-sm border-gray-300 rounded-lg bg-gray-50 font-bold focus:ring-[#002845] focus:border-[#002845]">
                                                                        <option value="admin" selected>Admin</option>
                                                                        <option value="profesor">Profesor</option>
                                                                    </select>
                                                                    <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">Actualizar</button>
                                                                </form>
                                                                
                                                                <form action="{{ route('usuarios.eliminar', $admin->id) }}" method="POST" class="m-0">
                                                                    @csrf @method('DELETE')
                                                                    <button type="button" onclick="confirmarAccion(this, '¿Desactivar administrador?', 'Esta cuenta pasará a estado inactivo.', 'Sí, desactivar', '#dc2626')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">
                                                                        ❌ Desactivar
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
                                    <h3 class="text-xl font-extrabold text-[#002845] flex items-center">👨‍🏫 Docentes</h3>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach ($usuarios->where('rol', 'profesor') as $docente)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold shadow-inner">
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
                                                                    <option value="profesor" selected>Profesor</option>
                                                                    <option value="admin">Admin</option>
                                                                </select>
                                                                <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">Actualizar</button>
                                                            </form>

                                                            <form action="{{ route('usuarios.eliminar', $docente->id) }}" method="POST" class="m-0">
                                                                @csrf @method('DELETE')
                                                                <button type="button" onclick="confirmarAccion(this, '¿Desactivar a este profesor?', 'Su cuenta pasará a estado inactivo.', 'Sí, desactivar', '#dc2626')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 w-32 text-center rounded-lg transition text-xs shadow-md">
                                                                    ❌ Desactivar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($usuarios->where('rol', 'profesor')->isEmpty())
                                                <tr><td colspan="2" class="px-6 py-8 text-center text-sm font-bold text-gray-400">No hay profesores registrados.</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-gray-100 rounded-3xl shadow-inner border border-gray-300 overflow-hidden opacity-90">
                                <div class="bg-gray-200 px-6 py-4 border-b border-gray-300 flex justify-start items-center">
                                    <h3 class="text-xl font-extrabold text-gray-600 flex items-center">💤 Usuarios Inactivos</h3>
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
                                                                <button type="button" onclick="confirmarAccion(this, '¿Reactivar usuario?', 'Volverá a tener acceso al sistema.', 'Sí, reactivar', '#10b981')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-lg transition text-xs shadow-md">
                                                                    ✅ Reactivar
                                                                </button>
                                                            </form>

                                                            <form action="{{ route('usuarios.destruir', $inactivo->id) }}" method="POST" class="m-0">
                                                                @csrf @method('DELETE')
                                                                <button type="button" onclick="confirmarAccion(this, '¿Borrar definitivamente?', 'Esta acción no se puede deshacer.', 'Sí, borrar', '#dc2626')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition text-xs shadow-md ml-2">
                                                                    🗑️ Borrar
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

    <script>
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