<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#002845] leading-tight">
                Gestión de Usuarios y Roles
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-[#002845]">← Volver al Panel</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('exito'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm flex items-center">
                    <span class="text-green-800 font-bold text-lg mr-2">✅</span>
                    <span class="text-green-700 font-medium">{{ session('exito') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm flex items-center">
                    <span class="text-red-800 font-bold text-lg mr-2">❌</span>
                    <span class="text-red-700 font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border-t-4 border-[#002845]">
                <div class="p-6 md:p-8">
                    <h3 class="text-xl font-extrabold text-[#002845] mb-4">Usuarios Registrados en el Sistema</h3>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Correo Electrónico</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha de Registro</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Asignar Rol</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($usuarios as $usuario)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $usuario->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $usuario->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $usuario->created_at->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            
                                            <form action="{{ route('usuarios.actualizarRol', $usuario->id) }}" method="POST" class="flex items-center justify-center space-x-2">
                                                @csrf
                                                @method('PUT')
                                                
                                                <select name="rol" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#002845] focus:ring focus:ring-blue-200" @if($usuario->id === 1) disabled title="El administrador principal no puede ser modificado" @endif>
                                                    <option value="profesor" {{ $usuario->rol === 'profesor' ? 'selected' : '' }}>Profesor</option>
                                                    <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Administrador</option>
                                                </select>

                                                <button type="submit" class="bg-[#002845] text-[#FFD700] hover:bg-blue-900 px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm" @if($usuario->id === 1) disabled @endif>
                                                    Guardar
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>