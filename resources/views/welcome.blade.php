<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda U - Reservas Estudiantiles</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <style>
        .fc-theme-standard td, .fc-theme-standard th { border-color: #f3f4f6; }
        .fc-theme-standard .fc-scrollgrid { border-color: transparent; }
        .fc-event {
            border-radius: 6px;
            border: none;
            padding: 2px 4px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }
        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        .fc .fc-button-primary {
            background-color: #002845 !important;
            border-color: #002845 !important;
            text-transform: capitalize;
            border-radius: 8px;
            font-weight: 600;
        }
        .fc .fc-button-primary:hover {
            background-color: #001a2e !important;
        }
        .fc .fc-button-primary:disabled {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
        }
        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1f2937;
            text-transform: capitalize;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen font-sans text-gray-800">

    <nav class="bg-gradient-to-r from-[#002845] to-[#004273] text-white p-4 shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-3xl">🎓</span>
                <h1 class="text-2xl font-extrabold tracking-wide">Agenda U</h1>
            </div>
            
            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-white text-[#002845] px-5 py-2 rounded-full font-bold shadow hover:bg-gray-100 transition">Ir a mi Panel</a>
                    @else
                        <a href="{{ route('login', ['tipo' => 'docente']) }}" class="text-blue-100 hover:text-white font-semibold transition border-b-2 border-transparent hover:border-white pb-1">
                            👨‍🏫 Ingreso Docentes
                        </a>
                        
                        <a href="{{ route('login', ['tipo' => 'admin']) }}" class="bg-blue-800 border border-blue-400 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-900 transition shadow-md flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Admin
                        </a>
                    @endauth
                @endif
            </div>

        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 mt-4">
        
        <div class="mb-6">
            @if(session('exito'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-r-lg shadow-sm flex items-center">
                    <span class="text-2xl mr-3">✅</span>
                    <div>
                        <p class="font-bold">¡Reserva Exitosa!</p>
                        <p class="text-sm">{{ session('exito') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-lg shadow-sm flex items-center mt-4">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                        <p class="font-bold">Acceso Denegado</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full z-0"></div>
            
            <div class="relative z-10 mb-6 border-b border-gray-100 pb-4">
                <h2 class="text-2xl md:text-3xl font-extrabold text-[#002845]">📅 Programa tu Asesoría</h2>
                <p class="text-gray-500 mt-1">Navega por las semanas y haz clic sobre la clase a la que deseas asistir.</p>
            </div>
            
            <div id="calendar" class="relative z-10"></div>
        </div>
    </main>

    <div id="modalReserva" class="hidden fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all scale-100">
            
            <div class="bg-gradient-to-r from-[#002845] to-[#004273] px-6 py-5 text-center relative">
                <h3 class="text-xl font-extrabold text-white tracking-wide">Confirmar Asistencia</h3>
                <button onclick="cerrarModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('reservar.clase') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="horario_id" id="horario_id">
                <input type="hidden" name="fecha_reserva" id="fecha_reserva">

                <div class="mb-6 bg-blue-50 rounded-xl p-4 border border-blue-100 text-center">
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Has seleccionado:</p>
                    <p id="curso_nombre_display" class="text-xl font-extrabold text-[#002845] leading-tight"></p>
                    <div class="mt-2 flex items-center justify-center text-sm text-gray-600 space-x-4">
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> <span id="fecha_display"></span></span>
                    </div>
                    <div class="mt-1 flex items-center justify-center text-sm text-gray-600">
                         <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> <span id="lugar_display"></span></span>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 text-center mb-2">Ingresa tu número de Cédula</label>
                    <input type="text" name="cedula" placeholder="Ej: 1001234567" required class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-inner focus:border-[#002845] focus:ring focus:ring-blue-200 border-2 p-4 text-2xl text-center font-black tracking-widest text-gray-800 transition">
                    <p class="text-xs text-gray-500 mt-3 text-center flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        El sistema validará tu matrícula.
                    </p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="cerrarModal()" class="flex-1 bg-white text-gray-700 border-2 border-gray-200 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-[#002845] text-white px-4 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg shadow-blue-900/20 transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002845]">
                        Reservar Cupo
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'es',
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                allDaySlot: false,
                hiddenDays: [0], 
                expandRows: true, 
                slotLabelFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short' 
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana'
                },
                events: '{{ route("api.horarios") }}',
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    document.getElementById('horario_id').value = info.event.id;
                    let fechaClic = info.event.start.toISOString().split('T')[0];
                    document.getElementById('fecha_reserva').value = fechaClic;
                    document.getElementById('curso_nombre_display').innerText = info.event.title;
                    document.getElementById('fecha_display').innerText = fechaClic;
                    document.getElementById('lugar_display').innerText = info.event.extendedProps.lugar;
                    document.getElementById('modalReserva').classList.remove('hidden');
                }
            });
            calendar.render();
        });

        function cerrarModal() {
            document.getElementById('modalReserva').classList.add('hidden');
        }
    </script>
</body>
</html>