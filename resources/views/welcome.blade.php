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

        <div class="mb-8 flex flex-col md:flex-row justify-between items-center bg-blue-50 p-5 rounded-2xl border border-blue-100 shadow-sm">
            <div class="mb-4 md:mb-0">
                <h3 class="font-extrabold text-[#002845] text-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    ¿Ya tienes una reserva o deseas cancelarla?
                </h3>
                <p class="text-sm text-gray-600 mt-1">Ingresa con tu cédula para ver tus clases programadas y gestionar tus cupos.</p>
            </div>
            <a href="/estudiante" class="bg-[#FFD700] hover:bg-yellow-400 text-[#002845] font-bold py-2 px-6 rounded-xl shadow-md transition transform hover:-translate-y-0.5">
                Gestionar mis Clases
            </a>
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
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden transform transition-all scale-100">
            
            <div class="bg-gradient-to-r from-[#002845] to-[#004273] px-6 py-5 text-center relative">
                <h3 class="text-xl font-extrabold text-white tracking-wide uppercase">Confirmar Asistencia</h3>
                <button onclick="cerrarModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('reservar.clase') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="horario_id" id="horario_id">
                
                <input type="hidden" name="fecha" id="fecha_reserva_input">

                <div class="mb-8 text-center">
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-2">Estás reservando cupo en:</p>
                    <p id="curso_nombre_display" class="text-2xl font-black text-[#002845] leading-tight mb-2"></p>
                    
                    <div class="flex items-center justify-center text-sm text-gray-600 mb-6">
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFD700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> <span id="fecha_display" class="font-bold"></span></span>
                    </div>
                    
                    <div id="lugar_display" class="mt-4 relative z-20"></div>
                </div>

                <div class="mb-8 relative z-10">
                    <label class="block text-sm font-bold text-gray-700 text-center mb-2">Ingresa tu número de Cédula</label>
                    <input type="text" name="cedula" placeholder="Ej: 1001234567" required class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-inner focus:border-[#002845] focus:ring focus:ring-blue-200 border-2 p-4 text-2xl text-center font-black tracking-widest text-gray-800 transition">
                </div>

                <div class="flex space-x-3">
                    <button type="button" onclick="cerrarModal()" class="flex-1 bg-white text-gray-700 border-2 border-gray-200 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 bg-[#002845] text-white px-4 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg shadow-blue-900/20 transition transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#002845]">
                        Confirmar Cupo
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
                
                // 🔥 EVENT CLICK DE ALTO IMPACTO VISUAL 🔥
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    // Llenar inputs ocultos
                    document.getElementById('horario_id').value = info.event.id;
                    
                    // FIX: Extraer la fecha exacta en la que se hizo clic
                    let fechaClic = info.event.start.toISOString().split('T')[0];
                    document.getElementById('fecha_reserva_input').value = fechaClic; // Para el backend
                    
                    // Llenar display básico
                    document.getElementById('curso_nombre_display').innerText = info.event.title;
                    document.getElementById('fecha_display').innerText = fechaClic;
                    
                    // LÓGICA DE UBICACIÓN (Virtual vs Presencial con diseño masivo)
                    const props = info.event.extendedProps;
                    let htmlUbicacion = '';
                    let esVirtual = props.modalidad && props.modalidad.toString().trim().toLowerCase() === 'virtual';

                    if (esVirtual) {
                        htmlUbicacion = `
                            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-sky-600 rounded-2xl shadow-lg p-6 border-4 border-white transform transition hover:scale-[1.02]">
                                <div class="relative z-10 text-center">
                                    <span class="text-6xl mb-3 block drop-shadow-md">💻</span>
                                    <h4 class="text-4xl font-black text-white uppercase tracking-tighter mb-1">Clase <span class="text-sky-200">Virtual</span></h4>
                                    <p class="text-sm text-blue-50 font-medium">Revisa tu correo para el enlace de conexión.</p>
                                </div>
                            </div>
                        `;
                    } else {
                        htmlUbicacion = `
                            <div class="relative overflow-hidden bg-gradient-to-br from-[#FFD700] to-yellow-500 rounded-2xl shadow-lg p-6 border-4 border-white transform transition hover:scale-[1.02]">
                                <div class="relative z-10 text-center text-[#002845]">
                                    <span class="text-xl font-bold uppercase tracking-widest opacity-80 mb-1 block">🏢 Sede</span>
                                    <h4 class="text-5xl font-black uppercase tracking-tighter mb-4">${props.sede || 'Pascual'}</h4>
                                    
                                    <div class="flex justify-center space-x-4 border-t-2 border-[#002845]/20 pt-4 mt-4">
                                        <div class="border-r-2 border-[#002845]/20 pr-4">
                                            <span class="text-xs font-bold uppercase tracking-widest opacity-80 block mb-1">Bloque</span>
                                            <p class="text-4xl font-black leading-none">${props.bloque || 'N/A'}</p>
                                        </div>
                                        <div class="pl-2">
                                            <span class="text-xs font-bold uppercase tracking-widest opacity-80 block mb-1">Aula</span>
                                            <p class="text-4xl font-black leading-none">${props.aula || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Renderizamos el diseño masivo
                    document.getElementById('lugar_display').innerHTML = htmlUbicacion;
                    
                    // Abrimos el Modal
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