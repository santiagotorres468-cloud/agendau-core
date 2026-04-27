<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes Registrados - Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Manrope', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col hidden md:flex shadow-2xl z-20 flex-shrink-0">
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <div class="w-9 h-9 rounded-lg bg-[#C9A227] text-[#002845] font-extrabold text-sm flex items-center justify-center flex-shrink-0 tracking-tight">AU</div>
            <div>
                <p class="text-base font-bold text-white tracking-tight leading-tight">Agenda U</p>
                <p class="text-xs text-white/50 font-medium">Sistema de Asesorías</p>
            </div>
        </div>
        
        <div class="p-6">
            <p class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-2">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-xl font-bold shadow-inner uppercase">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> {{ auth()->user()->rol === 'admin' ? 'Administrador' : 'Docente' }}</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Centro de Control</span>
                </a>
                <a href="{{ route('seguimiento.index') }}" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <span class="text-lg">🔎</span>
                    <span>Seguimiento</span>
                </a>
                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Calendario Público</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="button" onclick="confirmarAccion(this, '¿Cerrar Sesión?', 'Saldrás del panel de gestión.', 'Sí, salir', '#002845')" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-slate-50">
        
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex flex-wrap justify-between items-center gap-4 z-10 sticky top-0">
            <div>
                <h2 class="text-2xl font-black text-[#002845] flex items-center">
                    <span class="text-3xl mr-3">📋</span> Gestión de Asistencia
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Estudiantes registrados en {{ $horario->curso_nombre }}</p>
            </div>
            
            <a href="{{ route('dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-[#002845] px-5 py-2.5 rounded-xl font-bold transition flex items-center border border-gray-300 shadow-sm text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver a Clases
            </a>
        </header>

        <div class="p-6 md:p-8 flex-1">
            <div class="max-w-7xl mx-auto">
                
                @if (session('exito'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm flex items-center">
                        <span class="text-green-800 font-bold text-lg mr-2">✅</span>
                        <span class="text-green-700 font-medium">{{ session('exito') }}</span>
                    </div>
                @endif

                @php
                    $diasIngles = [
                        'Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miercoles' => 'Wednesday', 'Miércoles' => 'Wednesday',
                        'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sabado' => 'Saturday', 'Sábado' => 'Saturday', 'Domingo' => 'Sunday'
                    ];
                    $diaEnIngles = $diasIngles[$horario->dia_semana] ?? 'Monday';
                @endphp

                <div class="bg-white rounded-3xl shadow-sm p-6 border-t-8 border-[#C9A227] mb-8 border border-gray-200">
                    <h2 class="text-2xl font-black text-[#002845] uppercase tracking-wide">{{ $horario->curso_nombre }}</h2>
                    <div class="mt-3 text-sm text-gray-600 flex flex-col md:flex-row gap-4 md:gap-8">
                        <p class="flex items-center">
                            <span class="text-xl mr-2">📅</span>
                            <span><strong class="text-gray-800">Fecha de clase:</strong><br> {{ \Carbon\Carbon::parse('next ' . $diaEnIngles)->format('d/m/Y') }}</span>
                        </p>
                        <p class="flex items-center">
                            <span class="text-xl mr-2">⏰</span>
                            <span><strong class="text-gray-800">Horario:</strong><br> {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</span>
                        </p>
                        <p class="flex items-center">
                            <span class="text-xl mr-2">📍</span>
                            <span>
                                <strong class="text-gray-800">Ubicación:</strong><br>
                                @if(strtolower(trim($horario->modalidad)) === 'virtual')
                                    <span class="text-blue-600 font-bold">Modalidad Virtual</span>
                                @else
                                    {{ $horario->sede ?: 'Sede N/A' }} | Bloque {{ $horario->bloque ?: '-' }} | Aula {{ $horario->aula ?: '-' }}
                                @endif
                            </span>
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="bg-[#002845] px-6 py-4 flex items-center">
                        <h3 class="text-lg font-bold text-white tracking-wide">Lista de Estudiantes Inscritos</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Documento</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Estudiante</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-gray-500 uppercase tracking-wider">Fecha de Reserva</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-gray-500 uppercase tracking-wider">Control de Asistencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($reservas ?? [] as $reserva)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-5 whitespace-nowrap text-sm font-black text-[#002845]">{{ $reserva->estudiante->cedula }}</td>
                                        <td class="px-6 py-5 text-sm text-gray-800 font-bold">
                                            {{ $reserva->estudiante->nombre_completo }}
                                            @if($reserva->calificacion)
                                                <span class="ml-2 bg-yellow-50 text-yellow-800 text-xs px-2 py-0.5 rounded-full font-black border border-yellow-200 shadow-sm whitespace-nowrap" title="Calificación del estudiante">
                                                    {{ str_repeat('⭐', $reserva->calificacion) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                                        
                                        <td class="px-6 py-5 text-center text-sm font-medium flex justify-center space-x-2">
                                            
                                            @if($reserva->estado === 'Programada')
                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline m-0">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="asistencia" value="1">
                                                    <button type="submit" class="bg-green-100 text-green-800 px-4 py-2 rounded-lg hover:bg-green-200 font-bold transition-all shadow-sm border border-green-300 text-xs">
                                                        ✅ Asistió
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.asistencia', $reserva->id) }}" method="POST" class="inline m-0">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="asistencia" value="0">
                                                    <button type="submit" class="bg-red-100 text-red-800 px-4 py-2 rounded-lg hover:bg-red-200 font-bold transition-all shadow-sm border border-red-300 text-xs">
                                                        ❌ Faltó
                                                    </button>
                                                </form>

                                                <form action="{{ route('reservas.eliminar', $reserva->id) }}" method="POST" class="inline m-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmarAccion(this, '¿Remover estudiante?', 'Se eliminará la inscripción de este estudiante a la clase.', 'Sí, remover', '#dc2626')" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-200 font-bold transition-all shadow-sm border border-gray-300 text-xs">
                                                        🗑️ Quitar
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex flex-col items-center space-y-1.5 w-full max-w-[140px] mx-auto">
                                                    <span class="w-full text-center px-4 py-1.5 rounded-lg font-black text-xs shadow-sm border {{ $reserva->asistencia ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                                        {{ $reserva->asistencia ? '✔️ PRESENTE' : '✖️ AUSENTE' }}
                                                    </span>
                                                    
                                                    <button onclick="document.getElementById('modal-{{ $reserva->id }}').classList.remove('hidden')" class="w-full justify-center bg-[#002845] text-white px-4 py-1.5 rounded-lg hover:bg-blue-900 font-bold shadow flex items-center text-xs transition-colors">
                                                        📝 Reporte
                                                    </button>

                                                    <form action="{{ route('reservas.corregir', $reserva->id) }}" method="POST" class="w-full m-0">
                                                        @csrf @method('PUT')
                                                        <button type="button" onclick="confirmarAccion(this, '¿Corregir asistencia?', 'El estado de este estudiante volverá a estar Pendiente.', 'Sí, corregir', '#f59e0b')" class="w-full justify-center text-[10px] text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 py-1 rounded border border-transparent hover:border-yellow-200 font-black uppercase tracking-widest transition-all flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                            Corregir
                                                        </button>
                                                    </form>

                                                    <div id="modal-{{ $reserva->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                            <div class="fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm transition-opacity" onclick="document.getElementById('modal-{{ $reserva->id }}').classList.add('hidden')"></div>
                                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                                            
                                                            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                <form action="{{ route('reservas.reporte', $reserva->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="bg-[#002845] px-6 py-4 border-b border-blue-900">
                                                                        <h3 class="text-xl font-black text-white" id="modal-title">Reporte de Evolución</h3>
                                                                    </div>
                                                                    <div class="bg-white px-6 pt-5 pb-6">
                                                                        <p class="text-sm font-bold text-gray-500 mb-4 border-b pb-2">Estudiante: <span class="text-gray-800">{{ $reserva->estudiante->nombre_completo }}</span></p>
                                                                        <div class="mt-2 text-left">
                                                                            <label class="block text-sm text-[#002845] mb-2 font-black">Escribe el progreso u observaciones:</label>
                                                                            <textarea name="evolucion" rows="4" required class="shadow-sm focus:ring-[#002845] focus:border-[#002845] block w-full sm:text-sm border-2 border-gray-200 rounded-xl p-4 transition-all outline-none" placeholder="Ej: El estudiante dominó los temas vistos en clase...">{{ $reserva->evolucion }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3 rounded-b-3xl">
                                                                        <button type="submit" name="accion" value="descargar" onclick="setTimeout(() => document.getElementById('modal-{{ $reserva->id }}').classList.add('hidden'), 800)" class="w-full inline-flex justify-center items-center rounded-xl shadow-md px-4 py-3 bg-[#002845] text-sm font-black text-white hover:bg-blue-900 focus:outline-none transition">
                                                                            📄 Descargar PDF
                                                                        </button>
                                                                        
                                                                        <button type="submit" name="accion" value="guardar" class="w-full inline-flex justify-center items-center rounded-xl shadow-md px-4 py-3 bg-[#10b981] text-sm font-black text-white hover:bg-emerald-600 focus:outline-none transition">
                                                                            💾 Solo Guardar
                                                                        </button>
                                                                        
                                                                        <button type="button" onclick="document.getElementById('modal-{{ $reserva->id }}').classList.add('hidden')" class="w-full inline-flex justify-center rounded-xl border-2 border-gray-200 shadow-sm px-4 py-3 bg-white text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                                                                            Cancelar
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                            <span class="text-5xl block mb-3 opacity-50">📭</span>
                                            <p class="font-black text-xl text-gray-700">Sin estudiantes</p>
                                            <p class="text-sm mt-1">Aún no hay estudiantes registrados para esta clase.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function confirmarAccion(buttonElement, titulo, texto, textoBotonConfirmar, colorBoton) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: colorBoton,
                cancelButtonColor: '#9ca3af',
                confirmButtonText: textoBotonConfirmar,
                cancelButtonText: 'Cancelar',
                customClass: {
                    title: 'font-extrabold text-[#002845]',
                    popup: 'rounded-3xl shadow-2xl border border-gray-100',
                    confirmButton: 'font-bold px-6 py-2.5 rounded-xl text-sm',
                    cancelButton: 'font-bold px-6 py-2.5 rounded-xl text-sm text-gray-700 bg-gray-200 hover:bg-gray-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    buttonElement.closest('form').submit();
                }
            })
        }
    </script>
</body>
</html>