<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#002845] leading-tight">
                Estudiantes Registrados - {{ $horario->curso_nombre }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-[#002845]">← Volver al Panel</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('exito'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm flex items-center">
                    <span class="text-green-800 font-bold text-lg mr-2">✅</span>
                    <span class="text-green-700 font-medium">{{ session('exito') }}</span>
                </div>
            @endif

            @php
                $diasIngles = [
                    'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miercoles' => 'Wednesday', 'Miércoles' => 'Wednesday',
                    'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sabado' => 'Saturday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
                ];
                $diaEnIngles = $diasIngles[$horario->dia_semana] ?? 'Monday';
            @endphp

            <div class="bg-white rounded-2xl shadow-sm p-6 border-l-8 border-[#FFD700] mb-6 border border-gray-100">
                <h2 class="text-2xl font-black text-[#002845] uppercase tracking-wide">{{ $horario->curso_nombre }}</h2>
                <div class="mt-2 text-sm text-gray-600 space-y-1">
                    <p>
                        <span class="font-bold text-gray-800">📅 Fecha de clase:</span> {{ \Carbon\Carbon::parse('next ' . $diaEnIngles)->format('d/m/Y') }} | 
                        <span class="font-bold text-gray-800">⏰ Horario:</span> {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                    </p>
                    <p>
                        <span class="font-bold text-gray-800">📍 Ubicación:</span> 
                        @if(strtolower(trim($horario->modalidad)) === 'virtual')
                            <span class="text-blue-600 font-bold">💻 Modalidad Virtual</span>
                        @else
                            Sede: <span class="font-semibold">{{ $horario->sede ?: 'No asignada' }}</span> | 
                            Bloque: <span class="font-semibold">{{ $horario->bloque ?: 'N/A' }}</span> | 
                            Aula: <span class="font-semibold">{{ $horario->aula ?: 'N/A' }}</span>
                        @endif
                    </p>
                </div>
                </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-[#002845]">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-extrabold text-[#002845] mb-4">Lista de Seguimiento Estudiantil</h3>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estudiante</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha de Reserva</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Seguimiento / Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reservas ?? [] as $reserva)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $reserva->estudiante->cedula }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reserva->estudiante->nombre_completo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-semibold">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                        
                                        <td class="px-6 py-4 text-center text-sm font-medium flex justify-center space-x-2">
                                            
                                            @if($reserva->estado === 'Programada')
                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="asistencia" value="1">
                                                    <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200 font-bold transition-all shadow-sm border border-green-300 text-xs">
                                                        ✅ Asistió
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="asistencia" value="0">
                                                    <button type="submit" class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 font-bold transition-all shadow-sm border border-red-300 text-xs">
                                                        ❌ Faltó
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.eliminar', $reserva->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas remover a este estudiante?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-gray-100 text-gray-500 px-3 py-1 rounded hover:bg-gray-200 font-bold transition-all shadow-sm border border-gray-300 text-xs">
                                                        🗑️ Quitar
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex flex-col items-center space-y-2">
                                                    <span class="px-3 py-1 rounded font-bold text-xs border shadow-sm {{ $reserva->asistencia ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                                        {{ $reserva->asistencia ? '✔️ Presente' : '✖️ Ausente' }}
                                                    </span>
                                                    
                                                    <div>
                                                        <button onclick="document.getElementById('modal-{{ $reserva->id }}').classList.remove('hidden')" class="bg-[#002845] text-white px-3 py-1.5 rounded-lg hover:bg-blue-800 font-bold shadow flex items-center text-xs transition-colors">
                                                            📝 Crear Reporte
                                                        </button>

                                                        <div id="modal-{{ $reserva->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                                
                                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('modal-{{ $reserva->id }}').classList.add('hidden')"></div>
                                                                
                                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                                                
                                                                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-8 border-[#002845]">
                                                                    
                                                                    <form action="{{ route('reservas.reporte', $reserva->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                            <h3 class="text-xl leading-6 font-black text-gray-900" id="modal-title">
                                                                                Reporte de Evolución
                                                                            </h3>
                                                                            <p class="text-sm font-bold text-gray-500 mt-1 mb-4 border-b pb-2">Estudiante: {{ $reserva->estudiante->nombre_completo }}</p>
                                                                            
                                                                            <div class="mt-2 text-left">
                                                                                <label class="block text-sm text-gray-700 mb-2 font-semibold">Escribe el progreso u observaciones de esta asesoría:</label>
                                                                                <textarea name="evolucion" rows="4" required class="shadow-sm focus:ring-[#002845] focus:border-[#002845] mt-1 block w-full sm:text-sm border border-gray-300 rounded-xl p-3" placeholder="Ej: El estudiante domina los conceptos básicos, pero necesita reforzar..."></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                                                                            <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-md px-4 py-2 bg-red-600 text-base font-bold text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                                                                📄 Guardar y Descargar
                                                                            </button>
                                                                            <button type="button" onclick="document.getElementById('modal-{{ $reserva->id }}').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                                Cancelar
                                                                            </button>
                                                                        </div>
                                                                    </form>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <form action="{{ route('reservas.corregir', $reserva->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-[10px] text-blue-600 hover:text-red-600 hover:underline font-bold uppercase tracking-tighter mt-1">
                                                            ¿Error? Corregir
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                            Aún no hay estudiantes registrados para esta clase.
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