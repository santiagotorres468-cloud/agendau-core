<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#002845] leading-tight">
            ✏️ Editar Clase y Asignar Profesor
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-6 md:p-10 border-t-4 border-[#002845]">

                <form action="{{ route('horarios.actualizar', $horario->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-8 bg-blue-50 p-5 rounded-xl border border-blue-200 shadow-sm">
                        <label for="user_id" class="block text-sm font-extrabold text-[#002845] mb-2 uppercase tracking-wide">
                            👨‍🏫 Asignar a una Cuenta de Profesor:
                        </label>
                        <select name="user_id" id="user_id" class="shadow-sm border-gray-300 focus:border-[#002845] focus:ring focus:ring-blue-200 rounded-lg w-full font-medium text-gray-700">
                            <option value="">-- Dejar sin asignar (NULL) --</option>
                            @foreach($profesores as $profe)
                                <option value="{{ $profe->id }}" {{ $horario->user_id == $profe->id ? 'selected' : '' }}>
                                    {{ $profe->name }} ({{ $profe->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2 font-semibold">Selecciona qué usuario podrá ver y gestionar esta clase en su panel.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Curso</label>
                            <input type="text" name="curso_nombre" value="{{ $horario->curso_nombre }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Docente (Etiqueta)</label>
                            <input type="text" name="docente_nombre" value="{{ $horario->docente_nombre }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Día de la Semana</label>
                            <input type="text" name="dia_semana" value="{{ $horario->dia_semana }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Lugar (Etiqueta antigua)</label>
                            <input type="text" name="lugar" value="{{ $horario->lugar }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hora Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ $horario->hora_inicio }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hora Fin</label>
                            <input type="time" name="hora_fin" value="{{ $horario->hora_fin }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" required>
                        </div>
                    </div>

                    <h3 class="font-extrabold text-[#002845] border-b-2 border-gray-200 pb-2 mb-4 mt-8">📍 Ubicación Específica</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <div class="md:col-span-4 lg:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Modalidad</label>
                            <select name="modalidad" id="modalidad_select" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 font-bold" onchange="toggleUbicacion()">
                                <option value="Presencial" {{ strtolower(trim($horario->modalidad)) !== 'virtual' ? 'selected' : '' }}>🏛️ Presencial</option>
                                <option value="Virtual" {{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'selected' : '' }}>💻 Virtual</option>
                            </select>
                        </div>
                        
                        <div id="ubicacion_fisica" class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4 transition-all duration-300">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Sede</label>
                                <input type="text" name="sede" value="{{ $horario->sede }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 placeholder-gray-400" placeholder="Ej: Robledo">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Bloque</label>
                                <input type="text" name="bloque" value="{{ $horario->bloque }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 placeholder-gray-400" placeholder="Ej: 4">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Aula</label>
                                <input type="text" name="aula" value="{{ $horario->aula }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 placeholder-gray-400" placeholder="Ej: 205">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('dashboard') }}" class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg font-bold hover:bg-gray-200 transition-colors border border-gray-300">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-[#002845] text-white px-5 py-2 rounded-lg font-bold hover:bg-[#001a2e] transition-colors shadow-md">
                            💾 Guardar y Asignar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function toggleUbicacion() {
            var modalidad = document.getElementById('modalidad_select').value;
            var contenedorFisico = document.getElementById('ubicacion_fisica');
            
            if (modalidad === 'Virtual') {
                contenedorFisico.style.opacity = '0.3';
                contenedorFisico.style.pointerEvents = 'none'; // Deshabilita los clics
            } else {
                contenedorFisico.style.opacity = '1';
                contenedorFisico.style.pointerEvents = 'auto'; // Habilita los clics
            }
        }
        
        // Ejecutar al cargar la página por si ya estaba en "Virtual"
        document.addEventListener('DOMContentLoaded', toggleUbicacion);
    </script>
</x-admin-layout>