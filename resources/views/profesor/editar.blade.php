<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-black text-2xl text-[#002845] leading-tight flex items-center">
                ✏️ Editar Clase: {{ $horario->curso_nombre }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-[#002845] transition">← Volver al Panel</a>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                
                <div class="bg-gradient-to-r from-[#002845] to-[#004273] px-8 py-6 text-white text-center">
                    <span class="text-4xl block mb-2">⚙️</span>
                    <h3 class="text-2xl font-black tracking-wide">Modificar Detalles del Horario</h3>
                </div>

                <form action="{{ route('horarios.actualizar', $horario->id) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Curso</label>
                            <input type="text" name="curso_nombre" value="{{ $horario->curso_nombre }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 transition font-medium">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Asignar Docente</label>
                            <select name="user_id" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 transition font-medium">
                                @foreach($profesores as $profesor)
                                    <option value="{{ $profesor->id }}" {{ $horario->user_id == $profesor->id ? 'selected' : '' }}>
                                        {{ $profesor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <input type="hidden" name="docente_nombre" value="{{ $horario->docente_nombre }}">

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Día de la Semana</label>
                            <select name="dia_semana" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200 transition font-medium">
                                @foreach(['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'] as $dia)
                                    <option value="{{ $dia }}" {{ $horario->dia_semana === $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Hora de Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ $horario->hora_inicio }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Hora de Fin</label>
                            <input type="time" name="hora_fin" value="{{ $horario->hora_fin }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <h4 class="text-lg font-black text-[#002845] mb-4">📍 Detalles de Ubicación</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Modalidad</label>
                                <select name="modalidad" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium" onchange="toggleUbicacion(this.value)">
                                    <option value="Presencial" {{ strtolower(trim($horario->modalidad)) !== 'virtual' ? 'selected' : '' }}>Presencial</option>
                                    <option value="Virtual" {{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>

                            <div id="campos_fisicos" class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6" style="{{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'display: none;' : '' }}">
                                <div class="col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Sede</label>
                                    <input type="text" name="sede" value="{{ $horario->sede }}" placeholder="Ej: Robledo" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Bloque</label>
                                    <input type="text" name="bloque" value="{{ $horario->bloque }}" placeholder="Ej: 6" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium">
                                </div>
                                <div class="col-span-1">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Aula</label>
                                    <input type="text" name="aula" value="{{ $horario->aula }}" placeholder="Ej: 401" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-[#002845] font-medium">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('dashboard') }}" class="bg-white text-gray-700 border-2 border-gray-200 px-6 py-3 rounded-xl font-bold hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-[#002845] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg transition transform hover:-translate-y-0.5">
                            💾 Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleUbicacion(modalidad) {
            const camposFisicos = document.getElementById('campos_fisicos');
            if (modalidad === 'Virtual') {
                camposFisicos.style.display = 'none';
            } else {
                camposFisicos.style.display = 'grid';
            }
        }
    </script>
</x-app-layout>