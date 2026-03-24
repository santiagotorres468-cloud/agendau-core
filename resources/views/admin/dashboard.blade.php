<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col">
            <h2 class="font-black text-2xl text-[#002845] leading-tight flex items-center">
                <svg class="w-8 h-8 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Centro de Control
            </h2>
            <p class="text-gray-500 text-sm mt-1 ml-11">Gestiona los permisos, clases y acceso de los usuarios.</p>
        </div>
    </x-slot>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('exito'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 15000)" x-show="show" x-transition.duration.500ms class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg shadow-sm flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <p class="font-bold">{{ session('exito') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-transition.duration.500ms
                     class="mb-6 bg-red-50 text-red-800 px-6 py-4 rounded-lg font-bold border border-red-300 shadow-sm flex flex-col relative">
                    
                    <button @click="show = false" class="absolute top-2 right-2 text-red-500 hover:text-red-900">
                        ✖️
                    </button>

                    <div class="flex items-center mb-2">
                        <span class="mr-2 text-xl">⚠️</span> 
                        <span>{{ session('error') }}</span>
                    </div>
                    
                    @if(session('lista_errores'))
                        <ul class="list-disc pl-8 text-sm font-medium text-red-700 space-y-1 mt-2">
                            @foreach(session('lista_errores') as $detalle)
                                <li>{{ $detalle }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <div x-data="{ tab: 'usuarios' }" class="w-full">
                
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

                <div x-show="tab === 'clases'" style="display: none;" class="transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($horarios as $clase)
                            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transform transition duration-300 hover:scale-[1.02] flex flex-col">
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
                                    </div>
                                    <div class="mt-auto grid grid-cols-2 gap-2 pt-4 border-t border-gray-100">
                                        <a href="{{ route('horarios.estudiantes', $clase->id) }}" class="col-span-2 bg-[#002845] text-center text-white font-bold py-2 rounded-xl hover:bg-blue-900 transition shadow">
                                            📋 Ver Estudiantes
                                        </a>
                                        @if(auth()->user()->rol === 'admin')
                                            <a href="{{ route('horarios.editar', $clase->id) }}" class="bg-yellow-100 text-yellow-800 text-center font-bold py-2 rounded-xl hover:bg-yellow-200 transition text-sm flex items-center justify-center">✏️ Editar</a>
                                            <form action="{{ route('horarios.eliminar', $clase->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta clase?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-full bg-red-100 text-red-700 text-center font-bold py-2 rounded-xl hover:bg-red-200 transition text-sm flex items-center justify-center">🗑️ Borrar</button>
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
                                
                                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-xl shadow-sm">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="text-xl">⚠️</span>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-black text-yellow-800 uppercase tracking-wide">Aviso de Responsabilidad</h3>
                                            <div class="mt-1 text-xs text-yellow-700 font-medium">
                                                La importación masiva afecta directamente la base de datos de <strong>Agenda U</strong>. Cualquier cambio, alteración o reemplazo de datos se realiza bajo la absoluta responsabilidad del administrador en turno.
                                            </div>
                                        </div>
                                    </div>
                                </div>

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

                    <div x-show="tab === 'usuarios'" class="transition-all duration-300">
                        
                        <div class="mb-10 bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="bg-purple-50 px-6 py-4 border-b border-purple-100 flex justify-between items-center">
                                <h3 class="text-xl font-black text-purple-900 flex items-center">👑 Administradores</h3>
                                <span class="bg-purple-100 text-purple-800 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Seguridad Nivel 1</span>
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
                                                                @if(auth()->id() === $admin->id) <span class="text-blue-500 font-black text-xs ml-1">(TÚ)</span> @endif
                                                            </div>
                                                            <div class="text-xs text-gray-500">{{ $admin->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="flex items-center justify-end space-x-3 pr-4">
                                                        
                                                        @if($totalAdmins <= 1)
                                                            <button disabled class="bg-red-50 text-red-500 font-black py-2 px-5 rounded-lg border border-red-200 text-xs flex items-center cursor-not-allowed shadow-inner opacity-90">
                                                                🛡️ ÚNICO ADMIN (INBORRABLE)
                                                            </button>
                                                        @else
                                                            <form action="{{ route('usuarios.actualizarRol', $admin->id) }}" method="POST" class="flex items-center space-x-2">
                                                                @csrf @method('PUT')
                                                                <select name="rol" class="text-sm border-gray-300 rounded-lg bg-gray-50 font-bold focus:ring-[#002845] focus:border-[#002845]">
                                                                    <option value="admin" selected>Admin</option>
                                                                    <option value="profesor">Profesor</option>
                                                                </select>
                                                                <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 px-4 rounded-lg transition text-xs shadow-sm">Actualizar</button>
                                                            </form>
                                                            
                                                            <form action="{{ route('usuarios.eliminar', $admin->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas desactivar a este administrador? @if(auth()->id() === $admin->id) ¡CUIDADO, ERES TÚ MISMO! @endif')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black py-2 px-4 rounded-lg transition text-xs shadow-md">
                                                                    ❌ DESACTIVAR
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

                        <div class="bg-white rounded-3xl shadow-md border border-gray-200 overflow-hidden">
                            <div class="bg-blue-50 px-6 py-4 border-b border-blue-100 flex justify-between items-center">
                                <h3 class="text-xl font-black text-[#002845] flex items-center">👨‍🏫 Profesores</h3>
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Docentes</span>
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
                                                            <button type="submit" class="bg-[#FFD700] hover:bg-yellow-500 text-[#002845] font-bold py-2 px-4 rounded-lg transition text-xs shadow-sm">Actualizar</button>
                                                        </form>

                                                        <form action="{{ route('usuarios.eliminar', $docente->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas desactivar a este profesor?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-black py-2 px-4 rounded-lg transition text-xs shadow-md">
                                                                ❌ DESACTIVAR
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

                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>