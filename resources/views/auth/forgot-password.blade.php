<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden">

        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>

        <div class="w-full sm:max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden z-10 mx-4">

            <!-- Header idéntico al login -->
            <div class="bg-gradient-to-r from-[#002845] to-blue-900 px-8 py-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 16px 16px;"></div>
                <span class="text-6xl relative z-10 drop-shadow-md block mb-2">🔑</span>
                <h2 class="text-3xl font-black text-white relative z-10 tracking-wide">Recuperar acceso</h2>
                <p class="text-blue-200 mt-2 text-sm font-medium relative z-10">Te enviaremos un enlace para restablecer tu contraseña</p>
            </div>

            <div class="p-8">

                <!-- Error de validación -->
                <x-validation-errors class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 text-sm font-bold" />

                <!-- Confirmación de envío -->
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl flex items-start gap-3">
                        <span class="text-xl mt-0.5">✅</span>
                        <div>
                            <p class="font-black text-sm">¡Enlace enviado!</p>
                            <p class="text-sm font-medium mt-0.5">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block font-bold text-sm text-[#002845] mb-1">
                            Correo Electrónico
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="tu@correo.com"
                            class="block w-full border-gray-300 focus:border-[#002845] focus:ring focus:ring-[#002845] focus:ring-opacity-20 rounded-xl shadow-sm px-4 py-3 text-gray-700 transition"
                        />
                        <p class="text-xs text-gray-400 mt-1.5 font-medium">
                            Ingresa el correo con el que accedes al sistema.
                        </p>
                    </div>

                    <div class="pt-4 mt-2 border-t border-gray-100 space-y-3">
                        <button type="submit" class="w-full flex justify-center items-center bg-[#002845] hover:bg-blue-900 text-white font-black py-4 px-4 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                            Enviar enlace de recuperación
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </button>

                        <a href="{{ route('login') }}" class="w-full flex justify-center items-center text-sm text-gray-500 hover:text-[#002845] font-bold transition py-2">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Volver al inicio de sesión
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-400 text-xs font-bold z-10">
            &copy; {{ date('Y') }} Agenda U. Todos los derechos reservados.
        </div>
    </div>
</x-guest-layout>
