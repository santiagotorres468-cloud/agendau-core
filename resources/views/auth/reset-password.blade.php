<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden">

        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>

        <div class="w-full sm:max-w-md bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden z-10 mx-4">

            <!-- Header -->
            <div class="bg-gradient-to-r from-[#002845] to-blue-900 px-8 py-8 text-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(white 1px, transparent 1px); background-size: 16px 16px;"></div>
                <span class="text-6xl relative z-10 drop-shadow-md block mb-2">🔐</span>
                <h2 class="text-3xl font-black text-white relative z-10 tracking-wide">Nueva contraseña</h2>
                <p class="text-blue-200 mt-2 text-sm font-medium relative z-10">Elige una contraseña segura para tu cuenta</p>
            </div>

            <div class="p-8">

                <x-validation-errors class="mb-6 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 text-sm font-bold" />

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email (solo lectura, viene del enlace) -->
                    <div>
                        <label for="email" class="block font-bold text-sm text-[#002845] mb-1">
                            Correo Electrónico
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $request->email) }}"
                            required
                            readonly
                            class="block w-full border-gray-200 bg-gray-50 rounded-xl shadow-sm px-4 py-3 text-gray-500 cursor-not-allowed text-sm"
                        />
                    </div>

                    <!-- Nueva contraseña -->
                    <div>
                        <label for="password" class="block font-bold text-sm text-[#002845] mb-1">
                            Nueva contraseña
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres"
                            class="block w-full border-gray-300 focus:border-[#002845] focus:ring focus:ring-[#002845] focus:ring-opacity-20 rounded-xl shadow-sm px-4 py-3 text-gray-700 transition"
                        />
                    </div>

                    <!-- Confirmar contraseña -->
                    <div>
                        <label for="password_confirmation" class="block font-bold text-sm text-[#002845] mb-1">
                            Confirmar contraseña
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Repite la contraseña"
                            class="block w-full border-gray-300 focus:border-[#002845] focus:ring focus:ring-[#002845] focus:ring-opacity-20 rounded-xl shadow-sm px-4 py-3 text-gray-700 transition"
                        />
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full flex justify-center items-center bg-[#002845] hover:bg-blue-900 text-white font-black py-4 px-4 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                            Guardar nueva contraseña
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-400 text-xs font-bold z-10">
            &copy; {{ date('Y') }} Agenda U. Todos los derechos reservados.
        </div>
    </div>
</x-guest-layout>
