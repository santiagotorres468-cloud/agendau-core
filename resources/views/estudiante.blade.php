<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Calendario - Agenda U</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        .fc-theme-standard td, .fc-theme-standard th { border-color: #e5e7eb; }
        .fc-event { border-radius: 6px; border: none; padding: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s; }
        .fc-event:hover { transform: scale(1.02); }
        .fc .fc-button-primary { background-color: #002845 !important; border-color: #002845 !important; border-radius: 8px; font-weight: bold; }
        .fc .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 800 !important; color: #1f2937; text-transform: capitalize; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col hidden md:flex shadow-2xl z-20">
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <span class="text-3xl">🎓</span>
            <h1 class="text-2xl font-black tracking-wide">Agenda U</h1>
        </div>
        
        <div class="p-6">
            <p class="text-xs text-blue-300 font-bold uppercase tracking-wider mb-2">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-xl font-bold shadow-inner uppercase">
                    {{ substr(session('estudiante_nombre', 'E'), 0, 1) }}
                </div>
                <div>
                    <p class="font-bold text-sm truncate" title="{{ session('estudiante_nombre') }}">{{ session('estudiante_nombre', 'Estudiante') }}</p>
                    <p class="text-xs text-blue-200 flex items-center"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> Conectado</p>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="/estudiante" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold transition shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Mis Reservas</span>
                </a>
                <a href="/" class="flex items-center space-x-3 text-blue-200 hover:bg-blue-800 hover:text-white px-4 py-3 rounded-xl font-bold transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Explorar Clases</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-blue-900/50">
            <form action="{{ route('estudiante.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold transition w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex justify-between items-center z-10">
            <div>
                <h2 class="text-2xl font-extrabold text-[#002845]">📅 Mis Clases Programadas</h2>
                <p class="text-gray-500 text-sm mt-1">Haz clic sobre una clase para agregarla a tu Google Calendar o cancelarla.</p>
            </div>
            
            @if(session('exito'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold border border-green-300 shadow-sm animate-bounce">
                    {{ session('exito') }}
                </div>
            @endif

            <a href="/" class="bg-[#002845] text-white px-5 py-2 rounded-xl font-bold hover:bg-[#001a2e] transition shadow-md flex items-center ml-4">
                <span class="text-xl mr-2">+</span> Nueva Reserva
            </a>
        </header>

        <div class="p-6 flex-1 bg-slate-50">
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 h-full relative z-0">
                <div id="calendar" class="h-full"></div>
            </div>
        </div>
    </main>

    <div id="modalDetalle" class="hidden fixed inset-0 bg-slate-900 bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
            <div class="bg-emerald-500 px-6 py-4 text-center relative">
                <h3 class="text-lg font-extrabold text-white uppercase tracking-wide">Detalles de la Clase</h3>
                <button onclick="cerrarModal()" class="absolute top-4 right-4 text-white hover:text-gray-200 transition transform hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Materia Confirmada</p>
                <p id="curso_nombre" class="text-2xl font-black text-[#002845] leading-tight mb-4"></p>
                
                <div id="info_ubicacion" class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6 shadow-inner">
                    </div>

                <a id="btnGoogleCalendar" href="#" target="_blank" class="w-full flex items-center justify-center bg-white text-gray-700 border border-gray-300 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 transition mb-3 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Añadir a mi Google Calendar
                </a>

                <form id="formCancelar" action="" method="POST" onsubmit="return confirm('¿Estás totalmente seguro de cancelar tu cupo? Esta acción no se puede deshacer.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-xl font-bold hover:bg-red-100 hover:text-red-700 transition">
                        ❌ Cancelar mi Reserva
                    </button>
                </form>
            </div>
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
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana' },
                
                events: '{{ route("api.mis.horarios") }}',
                
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    document.getElementById('curso_nombre').innerText = info.event.title;
                    const props = info.event.extendedProps;
                    
                    let htmlUbicacion = '';
                    let esVirtual = props.modalidad && props.modalidad.toString().trim().toLowerCase() === 'virtual';
                    
                    if (esVirtual) {
                        htmlUbicacion = `<p class="font-bold text-blue-600 text-lg">💻 Modalidad Virtual</p><p class="text-xs text-gray-500 mt-1">Revisa tu correo para el enlace</p>`;
                    } else {
                        htmlUbicacion = `
                            <p class="font-bold text-gray-700 text-sm">🏢 Sede: ${props.sede || 'Pascual Bravo'}</p>
                            <p class="font-bold text-[#002845] text-lg">Bloque ${props.bloque || '-'} | Aula ${props.aula || '-'}</p>
                        `;
                    }
                    document.getElementById('info_ubicacion').innerHTML = htmlUbicacion;

                    function formatLocalForGoogle(date) {
                        if (!date) return '';
                        let pad = (n) => n < 10 ? '0'+n : n;
                        return date.getFullYear().toString() +
                               pad(date.getMonth() + 1) +
                               pad(date.getDate()) + 'T' +
                               pad(date.getHours()) +
                               pad(date.getMinutes()) +
                               pad(date.getSeconds());
                    }

                    let startStr = formatLocalForGoogle(info.event.start);
                    let endStr = formatLocalForGoogle(info.event.end || info.event.start);

                    let tituloUrl = encodeURIComponent("Tutoría: " + info.event.title);
                    let detallesUrl = encodeURIComponent("Reserva generada desde Agenda U.\n¡Recuerda asistir a tu tutoría!");
                    let ubicacionUrl = encodeURIComponent(esVirtual ? 'Modalidad Virtual' : `Sede ${props.sede || ''}, Bloque ${props.bloque || ''}, Aula ${props.aula || ''}`);

                    document.getElementById('btnGoogleCalendar').href = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${tituloUrl}&dates=${startStr}/${endStr}&details=${detallesUrl}&location=${ubicacionUrl}`;

                    // 🔥 CORRECCIÓN APLICADA AQUÍ: LA RUTA CORRECTA 🔥
                    document.getElementById('formCancelar').action = '/estudiante/reservas/' + info.event.id;
                    
                    document.getElementById('modalDetalle').classList.remove('hidden');
                }
            });
            calendar.render();
        });

        function cerrarModal() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }
    </script>
</body>
</html>