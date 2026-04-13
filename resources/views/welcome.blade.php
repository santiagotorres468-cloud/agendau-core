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
        .fc-event { border-radius: 6px; border: none; padding: 2px 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s; }
        .fc-event:hover { transform: scale(1.02); box-shadow: 0 4px 6px rgba(0,0,0,0.15); }
        .fc .fc-button-primary { background-color: #002845 !important; border-color: #002845 !important; text-transform: capitalize; border-radius: 8px; font-weight: 600; }
        .fc .fc-button-primary:hover { background-color: #001a2e !important; }
        .fc-toolbar-title { font-size: 1.5rem !important; font-weight: 700 !important; color: #1f2937; text-transform: capitalize; }
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
                @if (Auth::check())
                    <a href="{{ url('/dashboard') }}" class="bg-white text-[#002845] px-5 py-2 rounded-full font-bold shadow hover:bg-gray-100 transition">Ir a mi Panel</a>
                
                @elseif (session('estudiante_nombre'))
                    <div class="flex items-center space-x-3">
                        <span class="text-blue-100 font-medium hidden md:inline">Hola, {{ explode(' ', session('estudiante_nombre'))[0] }}</span>
                        <a href="/estudiante" class="bg-[#FFD700] text-[#002845] px-5 py-2 rounded-full font-bold shadow hover:bg-yellow-400 transition transform hover:-translate-y-0.5">Mis Clases</a>
                        
                        <form action="{{ route('estudiante.logout') }}" method="POST" class="m-0 p-0 flex">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 font-bold transition flex items-center bg-transparent border-0" title="Cerrar Sesión">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>

                @elseif (session('correo_pendiente'))
                    <div class="flex items-center space-x-3">
                        <button onclick="document.getElementById('modalVincular').classList.remove('hidden')" class="bg-yellow-400 text-[#002845] px-4 py-2 rounded-full font-bold shadow-md animate-pulse text-sm hover:bg-yellow-500 transition">
                            ⚠️ Falta vincular Cédula
                        </button>
                        <form action="{{ route('estudiante.logout') }}" method="POST" class="m-0 p-0 flex">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 font-bold transition flex items-center bg-transparent border-0" title="Cancelar Registro">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </button>
                        </form>
                    </div>
                
                @else
                    <a href="{{ route('google.login') }}" class="flex items-center bg-white text-gray-700 px-4 py-2 rounded-full font-bold shadow-md hover:bg-gray-50 transition border border-gray-200">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <span class="hidden sm:inline">Acceso Estudiantes</span>
                    </a>
                    <a href="{{ route('login') }}" class="text-blue-100 hover:text-white font-semibold transition border-b-2 border-transparent hover:border-white pb-1 ml-4 hidden md:flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Acceso Administrativo
                    </a>
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
                        <p class="font-bold">¡Operación Exitosa!</p>
                        <p class="text-sm">{{ session('exito') }}</p>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-r-lg shadow-sm flex items-center mt-4">
                    <span class="text-2xl mr-3">⛔</span>
                    <div>
                        <p class="font-bold">Atención</p>
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
                
                <div class="flex flex-col md:flex-row gap-4 mt-6">
                    <div class="flex-1">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Buscar Clase</label>
                        <input type="text" id="filtroClase" placeholder="Ej: Cálculo, Programación..." class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 text-sm font-bold focus:border-[#002845] focus:bg-white outline-none transition-all shadow-sm">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1 ml-1">Modalidad</label>
                        <select id="filtroModalidad" class="w-full bg-gray-50 border-2 border-gray-100 rounded-xl p-3 text-sm font-bold focus:border-[#002845] focus:bg-white outline-none transition-all shadow-sm cursor-pointer">
                            <option value="todas">Todas las modalidades</option>
                            <option value="virtual">💻 Virtual</option>
                            <option value="presencial">🏢 Presencial</option>
                        </select>
                    </div>
                </div>

            </div>
            <div id="calendar" class="relative z-10"></div>
        </div>
    </main>

    <div id="modalVincular" class="hidden fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all scale-100">
            <div class="bg-[#FFD700] px-6 py-5 text-center relative">
                <h3 class="text-xl font-black text-[#002845] tracking-wide uppercase">Vincular Cuenta</h3>
                <button onclick="document.getElementById('modalVincular').classList.add('hidden')" class="absolute top-4 right-4 text-[#002845] hover:text-gray-700 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('estudiante.vincular') }}" method="POST" class="p-8">
                @csrf
                <div class="text-center mb-6">
                    <p class="text-gray-600 font-medium text-sm">Ingresa tu número de cédula para conectarla permanentemente con tu correo <b>{{ session('correo_pendiente') }}</b>.</p>
                </div>
                <div class="mb-6">
                    <input type="text" name="cedula" placeholder="Ej: 1001234567" required class="block w-full rounded-xl border-gray-300 bg-gray-50 shadow-inner focus:border-[#002845] focus:ring focus:ring-blue-200 border-2 p-4 text-2xl text-center font-black tracking-widest text-gray-800 transition">
                </div>
                <button type="submit" class="w-full bg-[#002845] text-white px-4 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg transition transform hover:-translate-y-0.5">
                    Verificar y Vincular
                </button>
            </form>
        </div>
    </div>

    <div id="modalReserva" class="hidden fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden transform transition-all scale-100">
            
            <div class="bg-gradient-to-r from-[#002845] to-[#004273] px-6 py-5 text-center relative">
                <h3 class="text-xl font-extrabold text-white tracking-wide uppercase">Gestión de Cupo</h3>
                <button onclick="cerrarModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('reservar.clase') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="horario_id" id="horario_id">
                <input type="hidden" name="fecha" id="fecha_reserva_input">

                <div class="mb-8 text-center">
                    <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-2">Clase Seleccionada:</p>
                    <p id="curso_nombre_display" class="text-2xl font-black text-[#002845] leading-tight mb-2"></p>
                    <div class="flex items-center justify-center text-sm text-gray-600 mb-6">
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFD700]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> <span id="fecha_display" class="font-bold"></span></span>
                    </div>
                    <div id="lugar_display" class="mt-4 relative z-20"></div>
                </div>

                <div class="mb-6 relative z-10">
                    @if(session('estudiante_nombre'))
                        <div class="bg-green-50 border-2 border-green-200 p-4 rounded-xl text-center">
                            <span class="text-green-800 font-bold block mb-1">✅ Identidad Verificada</span>
                            <p class="text-sm text-green-700">Sesión iniciada como: {{ session('estudiante_nombre') }}</p>
                        </div>
                    @elseif(session('correo_pendiente'))
                        <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-2xl text-center">
                            <p class="text-sm font-bold text-yellow-800 mb-2">Aún no puedes reservar.</p>
                            <p class="text-xs text-yellow-700">Por favor, haz clic en el botón amarillo <b>"⚠️ Falta vincular Cédula"</b> en la parte superior para completar tu registro primero.</p>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl text-center">
                            <p class="text-sm font-bold text-gray-700 mb-4">Para gestionar cupos, debes iniciar sesión con tu correo institucional.</p>
                            <a href="{{ route('google.login') }}" class="inline-flex items-center justify-center w-full bg-white text-gray-700 border border-gray-200 font-bold py-3 px-6 rounded-xl shadow-md transition transform hover:-translate-y-0.5 hover:shadow-lg">
                                Iniciar Sesión con Google
                            </a>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col space-y-3">
                    <div class="flex space-x-3">
                        <button type="button" onclick="cerrarModal()" class="flex-1 bg-white text-gray-700 border-2 border-gray-200 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition focus:outline-none">
                            Cerrar
                        </button>
                        @if(session('estudiante_nombre'))
                            <button type="submit" class="flex-1 bg-[#002845] text-white px-4 py-3 rounded-xl font-bold hover:bg-[#001a2e] shadow-lg transition transform hover:-translate-y-0.5 focus:outline-none">
                                Confirmar Cupo
                            </button>
                        @endif
                    </div>

                    @if(session('estudiante_nombre'))
                        <button type="submit" formaction="{{ route('estudiante.cancelar.publico') }}" class="w-full bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-xl font-bold hover:bg-red-100 hover:text-red-700 transition shadow-sm focus:outline-none">
                            ❌ Si ya tienes cupo, cancélalo aquí
                        </button>
                    @endif
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
                slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana' },
                
                events: '{{ route("api.horarios") }}',
                
                // ASIGNAR ATRIBUTOS PARA EL FILTRO
                eventDidMount: function(info) {
                    let mod = info.event.extendedProps.modalidad ? info.event.extendedProps.modalidad.toString().toLowerCase().trim() : '';
                    let titulo = info.event.title ? info.event.title.toLowerCase().trim() : '';
                    
                    info.el.setAttribute('data-modalidad', mod);
                    info.el.setAttribute('data-curso', titulo);
                },

                // RE-APLICAR FILTRO AL CAMBIAR DE SEMANA
                datesSet: function() {
                    setTimeout(aplicarFiltros, 50);
                },
                
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    document.getElementById('horario_id').value = info.event.id;
                    let fechaClic = info.event.start.toISOString().split('T')[0];
                    document.getElementById('fecha_reserva_input').value = fechaClic; 
                    document.getElementById('curso_nombre_display').innerText = info.event.title;
                    document.getElementById('fecha_display').innerText = fechaClic;
                    
                    const props = info.event.extendedProps;
                    let htmlUbicacion = props.modalidad && props.modalidad.toString().trim().toLowerCase() === 'virtual' 
                        ? `<div class="bg-gradient-to-br from-blue-500 to-sky-600 rounded-2xl shadow-lg p-4 text-center text-white"><span class="text-4xl mb-2 block">💻</span><h4 class="text-2xl font-black uppercase">Clase Virtual</h4></div>`
                        : `<div class="bg-gradient-to-br from-[#FFD700] to-yellow-500 rounded-2xl shadow-lg p-4 text-center text-[#002845]"><span class="text-sm font-bold uppercase tracking-widest block mb-1">🏢 Sede</span><h4 class="text-3xl font-black uppercase mb-2">${props.sede || 'Pascual'}</h4><div class="flex justify-center border-t border-[#002845]/20 pt-2"><div class="border-r border-[#002845]/20 pr-4"><span class="text-xs uppercase font-bold block">Bloque</span><span class="text-xl font-black">${props.bloque || '-'}</span></div><div class="pl-4"><span class="text-xs uppercase font-bold block">Aula</span><span class="text-xl font-black">${props.aula || '-'}</span></div></div></div>`;
                    
                    document.getElementById('lugar_display').innerHTML = htmlUbicacion;
                    document.getElementById('modalReserva').classList.remove('hidden');
                }
            });
            calendar.render();

            // 🔥 LA FUNCIÓN MÁGICA DEL FILTRO 🔥
            function aplicarFiltros() {
                let buscarTexto = document.getElementById('filtroClase').value.toLowerCase().trim();
                let modalidadSeleccionada = document.getElementById('filtroModalidad').value.toLowerCase().trim();
                
                let eventos = document.querySelectorAll('.fc-event');

                eventos.forEach(ev => {
                    let modEvento = ev.getAttribute('data-modalidad') || '';
                    let cursoEvento = ev.getAttribute('data-curso') || '';

                    let cumpleTexto = buscarTexto === '' || cursoEvento.includes(buscarTexto);
                    let cumpleModalidad = (modalidadSeleccionada === 'todas' || modEvento === modalidadSeleccionada);

                    // Atrapamos la "caja invisible" de FullCalendar para ocultarla por completo
                    let cajaContenedora = ev.closest('.fc-timegrid-event-harness') || ev.closest('.fc-daygrid-event-harness') || ev;

                    if (cumpleTexto && cumpleModalidad) {
                        cajaContenedora.style.setProperty('display', '', 'important');
                    } else {
                        cajaContenedora.style.setProperty('display', 'none', 'important');
                    }
                });
            }

            document.getElementById('filtroClase').addEventListener('input', aplicarFiltros);
            document.getElementById('filtroModalidad').addEventListener('change', aplicarFiltros);
        });

        function cerrarModal() { document.getElementById('modalReserva').classList.add('hidden'); }
    </script>
</body>
</html>