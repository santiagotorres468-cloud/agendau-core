<x-admin-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-[#002845] tracking-tight">Editar clase</h2>
            <p class="text-sm text-gray-500 font-medium mt-0.5">{{ $horario->curso_nombre }}</p>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border-t-8 border-[#002845]">
                
                <form action="{{ route('horarios.actualizar', $horario->id) }}" method="POST" class="p-8 md:p-10">
                    @csrf
                    @method('PUT')

                    <div class="mb-10 bg-blue-50 p-6 rounded-2xl border border-blue-100 shadow-inner">
                        <label for="user_id" class="block text-sm font-black text-[#002845] mb-3 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Asignar cuenta de docente
                        </label>
                        <select name="user_id" id="user_id" class="w-full bg-white border-2 border-blue-200 focus:border-[#002845] focus:ring focus:ring-blue-100 rounded-xl p-3 font-bold text-gray-700 shadow-sm transition">
                            <option value="">-- Dejar sin asignar (El profesor no podrá ver la clase en su panel) --</option>
                            @foreach($profesores as $profe)
                                <option value="{{ $profe->id }}" {{ $horario->user_id == $profe->id ? 'selected' : '' }}>
                                    {{ $profe->name }} ({{ $profe->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-blue-600 mt-3 font-medium bg-blue-50 p-2 rounded-lg inline-block">
                            El docente seleccionado podrá tomar asistencia y gestionar esta clase desde su panel.
                        </p>
                    </div>

                    <h3 class="font-black text-xl text-gray-800 border-b-2 border-gray-100 pb-3 mb-6">Detalles del Curso</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nombre del Curso</label>
                            <input type="text" name="curso_nombre" value="{{ $horario->curso_nombre }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nombre del Docente (Etiqueta)</label>
                            <input type="text" name="docente_nombre" value="{{ $horario->docente_nombre }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Día de la Semana</label>
                            <input type="text" name="dia_semana" value="{{ $horario->dia_semana }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Lugar (Etiqueta general)</label>
                            <input type="text" name="lugar" value="{{ $horario->lugar }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3 placeholder-gray-300" placeholder="Ej: Sede Robledo">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hora Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-black text-[#002845] p-3" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hora Fin</label>
                            <input type="time" name="hora_fin" value="{{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}" class="w-full bg-gray-50 rounded-xl border-gray-200 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-black text-[#002845] p-3" required>
                        </div>
                    </div>

                    <h3 class="font-black text-xl text-[#002845] border-b-2 border-gray-100 pb-3 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ubicación específica
                    </h3>
                    
                    <div class="bg-gray-100 p-6 rounded-2xl border border-gray-200 shadow-inner">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-4 lg:col-span-1">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Modalidad</label>
                                <select name="modalidad" id="modalidad_select" class="w-full bg-white rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-black text-[#002845] p-3 transition" onchange="toggleUbicacion()">
                                    <option value="Presencial" {{ strtolower(trim($horario->modalidad)) !== 'virtual' ? 'selected' : '' }}>Presencial</option>
                                    <option value="Virtual" {{ strtolower(trim($horario->modalidad)) === 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>
                            
                            <div id="ubicacion_fisica" class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4 transition-all duration-300">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sede</label>
                                    <input type="text" name="sede" value="{{ $horario->sede }}" class="w-full bg-white rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3 placeholder-gray-300" placeholder="Ej: Robledo">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Bloque</label>
                                    <input type="text" name="bloque" value="{{ $horario->bloque }}" class="w-full bg-white rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3 placeholder-gray-300" placeholder="Ej: 4">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Aula</label>
                                    <input type="text" name="aula" value="{{ $horario->aula }}" class="w-full bg-white rounded-xl border-gray-300 shadow-sm focus:border-[#002845] focus:ring focus:ring-gray-200 font-bold p-3 placeholder-gray-300" placeholder="Ej: 205">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end space-x-4 border-t border-gray-100 pt-6">
                        <a href="{{ route('dashboard') }}" class="bg-white text-gray-600 px-6 py-3 rounded-xl font-bold hover:bg-gray-100 hover:text-gray-900 transition-colors border-2 border-gray-200 shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-[#002845] text-white px-8 py-3 rounded-xl font-black hover:bg-[#001a2e] transition-all transform hover:-translate-y-1 shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Guardar cambios
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
            var inputs = contenedorFisico.querySelectorAll('input');
            
            if (modalidad === 'Virtual') {
                contenedorFisico.style.opacity = '0.4';
                contenedorFisico.style.pointerEvents = 'none'; // Deshabilita los clics
                contenedorFisico.classList.add('grayscale');
                // Opcional: Limpiar los campos si se pasa a virtual
                // inputs.forEach(input => input.value = ''); 
            } else {
                contenedorFisico.style.opacity = '1';
                contenedorFisico.style.pointerEvents = 'auto'; // Habilita los clics
                contenedorFisico.classList.remove('grayscale');
            }
        }
        
        // Ejecutar al cargar la página por si ya estaba guardada en "Virtual"
        document.addEventListener('DOMContentLoaded', toggleUbicacion);
    </script>
</x-admin-layout>