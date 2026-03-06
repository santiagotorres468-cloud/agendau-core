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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Curso</label>
                            <input type="text" name="curso_nombre" value="{{ $horario->curso_nombre }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Docente (Etiqueta)</label>
                            <input type="text" name="docente_nombre" value="{{ $horario->docente_nombre }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Día de la Semana</label>
                            <input type="text" name="dia_semana" value="{{ $horario->dia_semana }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Lugar / Bloque</label>
                            <input type="text" name="lugar" value="{{ $horario->lugar }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hora Inicio</label>
                            <input type="time" name="hora_inicio" value="{{ $horario->hora_inicio }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hora Fin</label>
                            <input type="time" name="hora_fin" value="{{ $horario->hora_fin }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
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
</x-admin-layout>