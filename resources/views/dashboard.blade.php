<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#002845] leading-tight">
            {{ __('Panel de Administración - I.U. Pascual Bravo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('exito'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm flex items-center">
                    <span class="text-green-800 font-bold text-lg mr-2">✅</span>
                    <span class="text-green-700 font-medium">{{ session('exito') }}</span>
                </div>
            @endif

            @if(auth()->user()->rol === 'admin')
                <div class="mb-6 flex justify-end">
                    <a href="{{ route('usuarios.index') }}" class="bg-[#002845] text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-900 transition-all shadow-md flex items-center">
                        👥 Gestionar Roles de Usuarios
                    </a>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border-t-4 border-[#FFD700]">
                    <div class="mb-6">
                        <h3 class="text-2xl font-extrabold text-[#002845] mb-2">Importar Horarios de Asesoría</h3>
                        <p class="text-gray-500 text-sm">
                            Selecciona el archivo de Excel <span class="font-mono bg-gray-100 px-1 rounded">.xlsx</span> proporcionado por la coordinación académica para actualizar la base de datos de clases de apoyo.
                        </p>
                    </div>
                    
                    <form action="{{ route('horarios.importar') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0 bg-gray-50 p-6 rounded-xl border border-gray-100">
                        @csrf
                        
                        <div class="w-full md:w-2/3">
                            <input type="file" name="archivo_excel" required accept=".xlsx, .xls, .csv"
                                   class="block w-full text-sm text-gray-600 
                                          file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 
                                          file:text-sm file:font-bold file:bg-[#002845] file:text-white 
                                          hover:file:bg-blue-900 hover:file:cursor-pointer transition-all shadow-sm">
                        </div>
                        
                        <div class="w-full md:w-1/3 text-right">
                            <button type="submit" class="w-full md:w-auto bg-[#FFD700] text-[#002845] px-8 py-3 rounded-full font-extrabold hover:bg-yellow-400 transition-all shadow-md flex justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                Subir Excel
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-[#002845]">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-extrabold text-[#002845] mb-4">Horarios Actuales en el Sistema</h3>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Curso</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Docente</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Día y Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Lugar</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($horarios as $horario)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $horario->curso_nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $horario->docente_nombre }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <span class="font-bold text-[#002845]">{{ $horario->dia_semana }}</span><br>
                                            {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('h:i A') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $horario->lugar }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center space-x-2">
                                            
                                            <a href="{{ route('horarios.estudiantes', $horario->id) }}" class="bg-[#002845] text-white px-3 py-2 rounded hover:bg-blue-900 transition-all shadow-sm" title="Ver Estudiantes">
                                                👁️ Ver
                                            </a>
                                            
                                            @if(auth()->user()->rol === 'admin')
                                                <a href="{{ route('horarios.editar', $horario->id) }}" class="bg-[#FFD700] text-[#002845] px-3 py-2 rounded hover:bg-yellow-400 font-bold transition-all shadow-sm" title="Editar">
                                                    ✏️ Editar
                                                </a>

                                                <form action="{{ route('horarios.eliminar', $horario->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta clase por completo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-800 font-bold transition-all shadow-sm" title="Eliminar">
                                                        🗑️ Borrar
                                                    </button>
                                                </form>
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                            Aún no hay horarios importados. Usa el formulario de arriba para subir tu archivo Excel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>