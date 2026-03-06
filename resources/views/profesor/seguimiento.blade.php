<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#002845] leading-tight">
            Módulo de Seguimiento Estudiantil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm flex items-center">
                    <span class="text-red-800 font-bold text-lg mr-2">⛔</span>
                    <span class="text-red-700 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-8">
                <div class="p-6 md:p-8 border-t-4 border-[#002845] bg-gray-50">
                    <h3 class="text-xl font-extrabold text-[#002845] mb-2">Buscar Estudiante</h3>
                    <p class="text-gray-600 text-sm mb-4">Ingresa el número de documento para consultar el historial y generar el reporte.</p>
                    
                    <form action="{{ route('seguimiento.buscar') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                        <input type="text" name="cedula" placeholder="Ej: 1020304050" required value="{{ request('cedula') }}"
                               class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-[#002845] focus:ring-opacity-50 p-3">
                        <button type="submit" class="bg-[#002845] text-white font-bold py-3 px-8 rounded-xl hover:bg-blue-900 transition shadow-md">
                            🔍 Consultar
                        </button>
                    </form>
                </div>
            </div>

            @if(isset($estudiante))
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    
                    <div class="bg-[#002845] p-6 text-white flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black uppercase tracking-wide">{{ $estudiante->nombre_completo }}</h2>
                            <p class="text-blue-200 mt-1 font-semibold">C.C. {{ $estudiante->cedula }}</p>
                        </div>
                    </div>

                    @php
                        $totalReservas = $historial->count();
                        $totalAsistidas = $historial->where('asistencia', 1)->count();
                        $totalFaltas = $historial->where('asistencia', 0)->whereNotNull('asistencia')->count();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-0 border-b border-gray-200 bg-gray-50 text-center">
                        <div class="p-6 border-r border-gray-200">
                            <span class="block text-sm font-bold text-gray-500 uppercase">Clases Agendadas</span>
                            <span class="block text-3xl font-black text-[#002845] mt-1">{{ $totalReservas }}</span>
                        </div>
                        <div class="p-6 border-r border-gray-200">
                            <span class="block text-sm font-bold text-gray-500 uppercase">Asistencias Confirmadas</span>
                            <span class="block text-3xl font-black text-green-600 mt-1">{{ $totalAsistidas }}</span>
                        </div>
                        <div class="p-6">
                            <span class="block text-sm font-bold text-gray-500 uppercase">Inasistencias</span>
                            <span class="block text-3xl font-black text-red-600 mt-1">{{ $totalFaltas }}</span>
                        </div>
                    </div>

                    <div class="p-6 md:p-8">
                        <h3 class="text-lg font-extrabold text-[#002845] mb-4 border-b pb-2">Historial Detallado de Clases</h3>
                        
                        @if($historial->isEmpty())
                            <p class="text-gray-500 italic text-center py-4">Este estudiante no tiene reservas registradas en tus clases.</p>
                        @else
                            <div class="overflow-x-auto border border-gray-200 rounded-lg mb-8">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Curso</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Estado de Asistencia</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Generar PDF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($historial as $reserva)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $reserva->horario->curso_nombre }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                    @if($reserva->estado === 'Programada')
                                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold border border-gray-200">Pendiente</span>
                                                    @elseif($reserva->asistencia === 1)
                                                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-xs font-bold border border-green-200">Presente</span>
                                                    @elseif($reserva->asistencia === 0)
                                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-xs font-bold border border-red-200">Ausente</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <form action="{{ route('reservas.reporte', $reserva->id) }}" method="POST" class="flex flex-col items-center">
                                                        @csrf
                                                        <textarea name="evolucion" placeholder="Nota rápida de evolución..." class="text-xs border-gray-300 rounded w-full mb-2 p-1 focus:ring-[#002845]"></textarea>
                                                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded hover:bg-red-700 font-bold text-xs flex items-center shadow-sm w-full justify-center">
                                                            📄 Reporte PDF
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            @endif

        </div>
    </div>
</x-admin-layout>