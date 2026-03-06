<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel Estudiantil para la gestión de clases y asesorías.">
    <title>Mi Panel Estudiantil | Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-slate-50 min-h-screen font-sans flex flex-col">

    <nav class="bg-gradient-to-r from-[#002845] to-[#004273] text-white p-4 shadow-lg mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            
            <div class="flex items-center space-x-3">
                <span class="text-3xl" aria-hidden="true">🎓</span>
                <h1 class="text-xl md:text-2xl font-extrabold tracking-wide">
                    Panel Estudiantil
                </h1>
            </div>

            <a href="{{ route('inicio') }}" 
               class="bg-white text-[#002845] hover:bg-gray-100 font-bold px-4 py-2 rounded-lg shadow-sm transition-colors duration-200">
                Volver al Calendario
            </a>
            
        </div>
    </nav>

    <main class="max-w-5xl mx-auto p-4 flex-grow w-full">
        
        <header class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-[#002845]">Gestiona tus Asesorías</h2>
            <p class="text-gray-600 mt-2 text-lg">
                Consulta tus clases reservadas o cancela tu asistencia si no puedes ir.
            </p>
        </header>

        @if (session('exito'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm flex items-center">
                <span class="text-green-800 font-bold text-xl mr-3">✅</span>
                <span class="text-green-700 font-medium">{{ session('exito') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm flex items-center">
                <span class="text-red-800 font-bold text-xl mr-3">⛔</span>
                <span class="text-red-700 font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <section class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-gray-100">
            <livewire:buscador-estudiante />
        </section>

    </main>

    <footer class="text-center py-6 text-gray-400 text-sm mt-auto">
        <p>&copy; {{ date('Y') }} Agenda U. Todos los derechos reservados.</p>
    </footer>

    @livewireScripts
</body>
</html>