<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - Agenda U</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#002845] to-[#004a7c] flex items-center justify-center p-4">

    <div class="w-full max-w-md">

        {{-- Logo / título --}}
        <div class="text-center mb-8">
            <span class="text-5xl">🔐</span>
            <h1 class="text-3xl font-black text-white mt-3">Agenda U</h1>
            <p class="text-blue-200 text-sm font-medium mt-1">Institución Universitaria Pascual Bravo</p>
        </div>

        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            <div class="bg-[#002845] px-8 py-6 text-center">
                <h2 class="text-white font-black text-xl">Establece tu contraseña</h2>
                <p class="text-blue-200 text-sm mt-1">Por seguridad, debes crear una contraseña personal antes de continuar.</p>
            </div>

            <div class="px-8 py-8">

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 text-sm font-semibold flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                    <span class="text-amber-500 text-lg flex-shrink-0">⚠️</span>
                    <div>
                        <p class="text-amber-800 font-bold text-sm">Acceso temporal detectado</p>
                        <p class="text-amber-700 text-xs mt-0.5">Tu cuenta fue creada con una contraseña provisional. Crea una contraseña segura que solo tú conozcas.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.forzar.actualizar') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Nueva contraseña</label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#002845] focus:border-transparent transition"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1.5">Confirmar contraseña</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Repite tu nueva contraseña"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#002845] focus:border-transparent transition"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#002845] hover:bg-[#001a2e] text-white font-black py-3.5 rounded-xl transition shadow-lg text-sm tracking-wide"
                    >
                        Guardar contraseña y entrar al sistema
                    </button>
                </form>

            </div>
        </div>

        <p class="text-center text-blue-300 text-xs mt-6">
            Sesión iniciada como <strong class="text-white">{{ auth()->user()->name }}</strong>
        </p>

    </div>

</body>
</html>
