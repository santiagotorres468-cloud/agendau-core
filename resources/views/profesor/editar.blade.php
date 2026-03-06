<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#002845] leading-tight">
                Editar Clase: {{ $horario->curso_nombre }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-[#002845]">← Volver</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border-t-4 border-[#FFD700]">
                
                <form action="{{ route('horarios.actualizar', $horario->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block text-[#002845] text-sm font-extrabold mb-2 uppercase tracking-wide">Profesor Asignado:</label>
                        <input type="text" name="docente_nombre" value="{{ $horario->docente_nombre }}" required
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200">
                    </div>

                    <div class="mb-8">
                        <label class="block text-[#002845] text-sm font-extrabold mb-2 uppercase tracking-wide">Lugar / Aula:</label>
                        <input type="text" name="lugar" value="{{ $horario->lugar }}" required
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200">
                    </div>

                    <div class="flex justify-end space-x-4 border-t border-gray-100 pt-6">
                        <a href="{{ route('dashboard') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition-all">Cancelar</a>
                        <button type="submit" class="bg-[#002845] text-[#FFD700] px-6 py-3 rounded-lg font-bold hover:bg-blue-900 transition-all shadow-md">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-admin-layout>