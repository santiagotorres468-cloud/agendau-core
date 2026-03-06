<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Asesorías | Pascual Bravo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased text-gray-900">

    <nav class="bg-[#002845] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-white text-xl font-bold tracking-wider border-l-4 border-[#FFD700] pl-3">
                        I.U. PASCUAL BRAVO
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-300 text-sm hidden md:block">Sistema de Clases de Apoyo</span>
                    <a href="{{ route('login') }}" class="text-[#002845] bg-[#FFD700] hover:bg-yellow-400 px-4 py-2 rounded-md font-semibold text-sm transition duration-150 ease-in-out shadow-sm">
                        Ingresar Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h1 class="text-3xl md:text-4xl font-extrabold text-[#002845] mb-4">
                    Consulta tus Horarios de Asesoría
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Ingresa tu número de cédula para verificar tu estado activo y visualizar las clases de apoyo disponibles.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 p-6 md:p-8">
                <livewire:buscador-estudiante />
            </div>
        </div>
    </main>

    @livewireScripts
</body>
</html>
