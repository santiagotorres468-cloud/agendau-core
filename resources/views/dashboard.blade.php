<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión - Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 flex h-screen overflow-hidden">

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
                    <p class="text-xs text-blue-200 flex items-center"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
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
                <button type="submit" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex justify-between items-center z-10 sticky top-0">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002845] flex items-center">
                    <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Centro de Control
                </h2>
                <p class="text-gray-500 text-sm mt-1">Gestiona tus clases y opciones de administración.</p>
            </div>
            
            @if(session('exito'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold border border-green-300 shadow-sm animate-bounce flex items-center">
                    <span class="mr-2">✅</span> {{ session('exito') }}
                </div>
            @endif
        </header>

        <div class="p-6 md:p-8 flex-1 bg-slate-50">
            <div class="max-w-7xl mx-auto">
                
                <div x-data="{ tab: 'clases' }" class="w-full">
                    
                    <div class="flex space-x-2 border-b-2 border-gray-200 mb-8 overflow-x-auto pb-2">
                        <button @click="tab = 'clases'" :class="tab === 'clases' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-extrabold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                            📚 Gestión de Clases
                        </button>
                        
                        @if(auth()->user()->rol === 'admin')
                            <button @click="tab = 'importar'" :class="tab === 'importar' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-extrabold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                📤 Importar Excel
                            </button>
                            <button @click="tab = 'usuarios'" :class="tab === 'usuarios' ? 'bg-[#002845] text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100'" class="px-6 py-3 rounded-xl font-extrabold text-sm transition-all whitespace-nowrap flex items-center cursor-pointer border border-transparent">
                                👥 Control de Roles
                            </button>
                        @endif
                    </div>

                    <div x-show="tab === 'clases'" class="transition-all duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse ($horarios as $clase)
                                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition duration-300 hover:scale-[1.02] hover:shadow-xl flex flex-col">
                                    <div class="bg-gradient-to-r from-[#002845] to-blue-900 px-6 py-4 flex justify-between items-center relative">
                                        <h3 class="text-lg font-black text-white truncate pr-4" title="{{ $clase->curso_nombre }}">{{ $clase->curso_nombre }}</h3>
                                        @if(strtolower(trim($clase->modalidad)) === 'virtual')
                                            <span class="absolute top-0 right-0 bg-blue-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm">VIRTUAL</span>
                                        @else
                                            <span class="absolute top-0 right-0 bg-[#FFD700] text-[#002845] text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm">PRESENCIAL</span>
                                        @endif
                                    </div>
                                    
                                    <div class="p-6 flex-1 flex flex-col">
                                        <div class="mb-4 text-sm text-gray-600 space-y-2">
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">👨‍🏫 Docente:</span> {{ $clase->docente_nombre }}</p>
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">📅 Día:</span> <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-bold border border-blue-100">{{ $clase->dia_semana }}</span></p>
                                            <p class="flex items-center"><span class="font-bold text-gray-800 w-20">⏰ Hora:</span> {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($clase->hora_fin)->format('H:i') }}</p>
                                            
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
                                                <form action="{{ route('horarios.eliminar', $clase->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta clase?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full bg-red-100 text-red-700 text-center font-bold py-2 rounded-xl hover:bg-red-200 transition text-sm flex items-center justify-center">
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
                                    <h3 class="text-xl font-bold text-gray-700">Aún no tienes clases asignadas</h3>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if(auth()->user()->rol === 'admin')
                        <div style="display: none;" x-show="tab === 'importar'" class="transition-all duration-300">
                            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden max-w-2xl mx-auto">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-8 py-6 text-white text-center">
                                    <span class="text-5xl block mb-2">📊</span>
                                    <h3 class="text-2xl font-black tracking-wide">Carga Masiva de Horarios</h3>
                                </div>
                                <form action="{{ route('horarios.importar') }}" method="POST" enctype="multipart/form-data" class="p-8">
                                    @csrf
                                    <div class="mb-8">
                                        <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-xl appearance-none cursor-pointer hover:bg-emerald-50">
                                            <span class="flex items-center space-x-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                                <span class="font-bold text-gray-600">Selecciona tu archivo Excel (.xlsx, .csv)</span>
                                            </span>
                                            <input type="file" name="archivo_excel" class="hidden" required accept=".xlsx,.xls,.csv" />
                                        </label>
                                    </div>
                                    <button type="submit" class="w-full bg-emerald-600 text-white font-black py-4 rounded-xl shadow-lg hover:bg-emerald-700 transition transform hover:-translate-y-1">
                                        🚀 Subir e Importar Datos
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div style="display: none;" x-show="tab === 'usuarios'" class="transition-all duration-300">
                            
                            <div class="mb-8 bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                                <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-xl font-black text-[#002845]">👑 Administradores</h3>
                                    <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Acceso Total</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-white">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase">Usuario</th>
                                                <th class="px-6 py-3 text-center text-xs font-black text-gray-500 uppercase">Rol Actual</th>
                                                <th class="px-6 py-3 text-center text-xs font-black text-gray-500 uppercase">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
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
                                                                    @if(auth()->id() === $admin->id) 
                                                                        <span class="text-blue-500 text-xs font-black ml-1">(TÚ)</span> 
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs text-gray-500">{{ $admin->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800">Administrador</span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        @if(auth()->id() === $admin->id)
                                                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wide bg-gray-100 px-3 py-2 rounded-lg">No Editable</span>
                                                        @else
                                                            <form action="{{ route('usuarios.actualizarRol', $admin->id) }}" method="POST" class="flex items-center justify-center space-x-2">
                                                                @csrf @method('PUT')
                                                                <select name="rol" class="block w-32 pl-3 pr-8 py-2 text-sm border-gray-300 rounded-lg bg-gray-50 font-bold cursor-pointer">
                                                                    <option value="admin" selected>Admin</option>
                                                                    <option value="profesor">Profesor</option>
                                                                </select>
                                                                <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 px-4 rounded-lg transition shadow-sm">Guardar</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-xl font-black text-[#002845]">👨‍🏫 Profesores</h3>
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Cuerpo Docente</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-white">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-black text-gray-500 uppercase">Usuario</th>
                                                <th class="px-6 py-3 text-center text-xs font-black text-gray-500 uppercase">Rol Actual</th>
                                                <th class="px-6 py-3 text-center text-xs font-black text-gray-500 uppercase">Acción</th>
                                            </tr>
                                        </thead>
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
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">Docente</span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                                        <form action="{{ route('usuarios.actualizarRol', $docente->id) }}" method="POST" class="flex items-center justify-center space-x-2">
                                                            @csrf @method('PUT')
                                                            <select name="rol" class="block w-32 pl-3 pr-8 py-2 text-sm border-gray-300 rounded-lg bg-gray-50 font-bold cursor-pointer">
                                                                <option value="profesor" selected>Profesor</option>
                                                                <option value="admin">Admin</option>
                                                            </select>
                                                            <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 px-4 rounded-lg transition shadow-sm">Guardar</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($usuarios->where('rol', 'profesor')->isEmpty())
                                                <tr><td colspan="3" class="px-6 py-6 text-center text-sm font-bold text-gray-400">No hay profesores registrados en el sistema.</td></tr>
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
</body>
</html>