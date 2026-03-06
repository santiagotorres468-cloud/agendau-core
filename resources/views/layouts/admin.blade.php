<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Pascual Bravo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased">
    
    <nav class="bg-[#002845] shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                <div class="flex items-center space-x-8">
                    <div class="flex items-center">
                        <span class="text-white text-xl font-bold tracking-wider border-l-4 border-[#FFD700] pl-3">
                            I.U. PASCUAL BRAVO
                        </span>
                    </div>
                    
                    <div class="hidden sm:flex space-x-4 mt-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-bold transition-colors {{ request()->routeIs('dashboard') ? 'text-[#FFD700] border-b-2 border-[#FFD700] pb-1' : 'text-gray-300 hover:text-white' }}">
                            Inicio 
                        </a>
                        <a href="{{ route('seguimiento.index') }}" class="px-3 py-2 text-sm font-bold transition-colors {{ request()->routeIs('seguimiento.*') ? 'text-[#FFD700] border-b-2 border-[#FFD700] pb-1' : 'text-gray-300 hover:text-white' }}">
                            🔍 Seguimiento Estudiantil
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-300 text-sm hidden md:block">Hola, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="text-[#002845] bg-[#FFD700] hover:bg-yellow-400 px-4 py-2 rounded-md font-bold text-sm transition shadow-sm">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @if (isset($header))
        <header class="bg-white shadow border-b border-gray-200">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main>
        {{ $slot }}
    </main>

</body>
</html>
