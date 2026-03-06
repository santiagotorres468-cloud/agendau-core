<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-extrabold text-[#002845] mb-4">
                    Consulta de Horarios de Asesoría
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Módulo de verificación. Ingresa el número de documento para visualizar las clases de apoyo disponibles.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-6 md:p-8">
                <livewire:buscador-estudiante />
            </div>

        </div>
    </div>
</x-admin-layout>