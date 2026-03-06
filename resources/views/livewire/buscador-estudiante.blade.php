<div>
    <div class="mb-8 relative">
        <label for="cedula" class="block text-sm font-bold text-[#002845] mb-2 uppercase tracking-wide">
            Número de Documento
        </label>
        <input type="text" id="cedula" wire:model.live="cedula"
               class="w-full px-5 py-4 text-lg rounded-xl border-2 border-gray-200 focus:ring-0 focus:border-[#FFD700] transition-colors duration-200 shadow-sm"
               placeholder="Ej: 1001001000">
               
        <div wire:loading wire:target="cedula" class="absolute right-4 top-11 text-[#002845] font-semibold animate-pulse">
            Buscando...
        </div>
    </div>

    @if($estudiante)
        <div class="bg-green-50 border-l-8 border-green-500 p-5 mb-6 rounded-r-xl shadow-sm transform transition-all duration-300">
            <h3 class="text-lg text-green-800 font-bold">
                ✅ Estudiante activo: {{ $estudiante->nombre_completo }}
            </h3>
            <p class="text-sm text-green-700 mt-1">
                Programa: <span class="font-semibold">{{ $estudiante->programa_academico }}</span>
            </p>
        </div>

        @if (session()->has('mensaje_reserva'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-r-xl shadow-sm font-bold flex items-center">
                <span class="text-xl mr-2">🎉</span> {{ session('mensaje_reserva') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white rounded-xl shadow-sm border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#002845]">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Docente</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Horario</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Lugar</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($horarios as $horario)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $horario->curso_nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $horario->docente_nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="font-bold text-[#002845] capitalize">
                                    {{ $this->calcularProximaFecha($horario->dia_semana) }}
                                </span><br>
                                {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#FFD700] text-[#002845]">
                                    {{ $horario->lugar }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="reservarCupo({{ $horario->id }})" 
                                        class="bg-[#002845] text-[#FFD700] hover:bg-blue-900 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-md">
                                    Reservar Cupo
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 italic">No hay clases de apoyo programadas por el momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif(strlen($cedula) >= 5)
        <div class="bg-red-50 border-l-8 border-red-500 p-5 rounded-r-xl shadow-sm transform transition-all duration-300">
            <h3 class="text-lg text-red-800 font-bold">
                ❌ Estudiante no encontrado
            </h3>
            <p class="text-sm text-red-700 mt-1">
                No figuras como activo en el sistema. Verifica que la cédula esté bien escrita.
            </p>
        </div>
    @endif
</div>