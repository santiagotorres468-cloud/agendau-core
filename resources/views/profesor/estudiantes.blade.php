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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-[#002845]">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-extrabold text-[#002845] mb-4">Lista de Asistencia</h3>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Estudiante</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha de Reserva</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reservas ?? [] as $reserva)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $reserva->estudiante->cedula }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reserva->estudiante->nombre_completo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-semibold">{{ $reserva->fecha }}</td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center space-x-2">
                                            
                                            @if($reserva->estado === 'Programada')
                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="asistencia" value="1">
                                                    <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200 font-bold transition-all shadow-sm border border-green-300 text-xs" title="Marcar como Presente">
                                                        ✅ Asistió
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="asistencia" value="0">
                                                    <button type="submit" class="bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 font-bold transition-all shadow-sm border border-red-300 text-xs" title="Marcar como Ausente">
                                                        ❌ Faltó
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.eliminar', $reserva->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas remover a este estudiante de la clase?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-gray-100 text-gray-500 px-3 py-1 rounded hover:bg-gray-200 font-bold transition-all shadow-sm border border-gray-300 text-xs" title="Eliminar Reserva">
                                                        🗑️ Quitar
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex flex-col items-center space-y-1">
                                                    <span class="px-3 py-1 rounded font-bold text-xs border shadow-sm 
                                                        {{ $reserva->asistencia ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                                        {{ $reserva->asistencia ? '✔️ Presente' : '✖️ Ausente' }}
                                                    </span>
                                                    
                                                    <form action="{{ route('reservas.corregir', $reserva->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="text-[10px] text-blue-600 hover:underline font-bold uppercase tracking-tighter">
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