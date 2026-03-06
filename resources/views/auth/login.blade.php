<x-guest-layout>
    <x-authentication-card>
        
        <x-slot name="logo">
            <div class="text-center pb-2">
                <span class="text-6xl">🎓</span>
                
                @if(request('tipo') == 'admin')
                    <h2 class="mt-4 text-3xl font-extrabold text-blue-900">🛡️ Portal de Administración</h2>
                    <p class="text-gray-500 mt-2 font-medium">Acceso exclusivo para gestión del sistema Agenda U.</p>
                @else
                    <h2 class="mt-4 text-3xl font-extrabold text-[#002845]">👨‍🏫 Portal Docente</h2>
                    <p class="text-gray-500 mt-2 font-medium">Ingresa para gestionar tus clases y asistencias.</p>
                @endif
            </div>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="hidden" name="tipo" value="{{ request('tipo') }}">

            <div>
                <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Contraseña') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-6 border-t pt-6 border-gray-100">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002845]" href="{{ route('password.request') }}">
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                @endif

                <x-button class="ms-4 bg-[#002845] hover:bg-[#001a2e] transition transform hover:-translate-y-0.5 shadow-md">
                    {{ __('Ingresar') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>