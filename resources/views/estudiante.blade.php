<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Calendario - Agenda U</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .fc-theme-standard td, .fc-theme-standard th { border-color: #e5e7eb; }
        
        /* Botones superiores del calendario */
        .fc .fc-button-primary { background-color: #002845 !important; border-color: #002845 !important; border-radius: 8px; font-weight: 700 !important; text-transform: capitalize; }
        .fc .fc-button-primary:hover { background-color: #001a2e !important; }
        .fc .fc-toolbar-title { font-size: 1.5rem !important; font-weight: 800 !important; color: #1e293b; }

        /* Tarjetas de Reserva Verdes (Diseño Estudiante) */
        .fc-timegrid-event { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; border: none !important; border-left: 5px solid #047857 !important; border-radius: 8px !important; cursor: pointer; text-align: center; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25) !important; transition: transform 0.2s ease, box-shadow 0.2s ease; overflow: hidden; }
        .fc-timegrid-event:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 8px 15px rgba(16, 185, 129, 0.4) !important; z-index: 20 !important; }
        .fc-timegrid-event .fc-event-main { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 8px !important; height: 100%; }
        
        .fc-v-event .fc-event-time { font-size: 0.8rem !important; font-weight: 700 !important; background-color: rgba(0, 0, 0, 0.15); color: #ffffff; padding: 4px 12px; border-radius: 20px; margin-bottom: 8px; letter-spacing: 0.5px; }
        .fc-v-event .fc-event-title { font-size: clamp(0.85rem, 1.2vw, 1.15rem) !important; font-weight: 800 !important; line-height: 1.3 !important; white-space: normal !important; width: 100%; text-shadow: 0 1px 2px rgba(0,0,0,0.2); }
    </style>

</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#002845] text-white flex flex-col shadow-2xl z-20 flex-shrink-0">
        <div class="p-6 flex items-center space-x-3 border-b border-blue-900/50">
            <span class="text-3xl">🎓</span>
            <h1 class="text-2xl font-black tracking-wide">Agenda U</h1>
        </div>
        <div class="p-6">
            <p class="text-xs text-blue-300 font-bold uppercase mb-2 tracking-wider">Mi Cuenta</p>
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xl shadow-inner">
                    {{ strtoupper(substr(session('estudiante_nombre', 'E'), 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ session('estudiante_nombre', 'Estudiante') }}</p>
                    <p class="text-xs text-blue-200 flex items-center font-medium"><span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span> Conectado</p>
                </div>
            </div>
            <nav class="space-y-2">
                <a href="/estudiante" class="flex items-center space-x-3 bg-blue-800 text-white px-4 py-3 rounded-xl font-bold shadow-md transition">
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
            <form action="{{ route('estudiante.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="flex items-center space-x-3 text-red-400 hover:text-red-300 font-bold w-full text-left transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white p-6 shadow-sm border-b border-gray-200 flex justify-between items-center z-10 sticky top-0">
            <div>
                <h2 class="text-2xl font-black text-[#002845]">📅 Mis Clases Programadas</h2>
                <p class="text-gray-500 text-sm mt-1 font-medium">Haz clic sobre una clase para ver los detalles o cancelar tu cupo.</p>
            </div>
            
            <div class="flex items-center gap-4">
                @if(session('exito')) 
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-bold shadow-sm border border-green-200">✅ {{ session('exito') }}</div> 
                @endif
                <a href="/" class="bg-[#002845] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-[#001a2e] transition shadow-md text-sm flex items-center">
                    <span class="text-lg mr-2">+</span> Nueva Reserva
                </a>
            </div>
        </header>

        <div class="p-6 flex-1 bg-slate-50">
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-gray-100 h-full relative z-0">
                <div id="calendar" class="h-full"></div>
            </div>
        </div>
    </main>

    <div id="modalDetalle" class="hidden fixed inset-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl transform transition-all border border-gray-100">
            
            <div class="bg-[#10b981] px-6 py-4 flex justify-between items-center relative">
                <h3 class="text-base font-black text-white uppercase tracking-widest w-full text-center">Detalles de la Clase</h3>
                <button onclick="document.getElementById('modalDetalle').classList.add('hidden')" class="absolute right-4 text-white hover:text-green-200 transition transform hover:scale-110 text-xl font-bold">
                    ✕
                </button>
            </div>
            
            <div class="p-8 text-center bg-white">
                <p class="text-xs text-gray-500 font-extrabold uppercase tracking-widest mb-1">Materia Confirmada</p>
                <p id="curso_nombre" class="text-2xl font-black text-[#002845] leading-tight mb-6 pb-4 border-b border-gray-100"></p>
                
                <div id="info_ubicacion" class="bg-gray-50 rounded-2xl p-5 border border-gray-200 mb-6 shadow-inner flex flex-col justify-center items-center h-24">
                    </div>

                <div class="space-y-3">
                    <a id="btnGoogleCalendar" href="#" target="_blank" class="w-full flex items-center justify-center bg-white text-gray-700 border border-gray-300 px-4 py-3 rounded-xl font-bold hover:bg-gray-50 transition shadow-sm text-sm">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Añadir a mi Google Calendar
                    </a>

                    <form id="formCancelar" action="" method="POST" class="m-0">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmarCancelacion(this)" class="w-full bg-red-50 text-red-600 border border-red-100 px-4 py-3 rounded-xl font-bold hover:bg-red-100 transition text-sm flex items-center justify-center">
                            <span class="mr-2 text-lg">❌</span> Cancelar mi Reserva
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
                locale: 'es', slotMinTime: '06:00:00', slotMaxTime: '22:00:00', allDaySlot: false, hiddenDays: [0], expandRows: true,
                headerToolbar: { left: 'prev,next today', center: 'title', right: window.innerWidth < 768 ? 'timeGridDay,timeGridWeek' : 'dayGridMonth,timeGridWeek' },
                buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
                events: '{{ route("api.mis.horarios") }}',
                
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    
                    document.getElementById('curso_nombre').innerText = info.event.title;
                    const props = info.event.extendedProps;
                    
                    let htmlUbicacion = '';
                    let esVirtual = props.modalidad && props.modalidad.toString().trim().toLowerCase() === 'virtual';
                    
                    if (esVirtual) {
                        htmlUbicacion = `<p class="font-bold text-blue-500 text-lg flex items-center justify-center"><span class="mr-2">💻</span> Modalidad Virtual</p><p class="text-xs text-gray-500 mt-1 font-medium">Revisa tu correo para el enlace.</p>`;
                    } else {
                        htmlUbicacion = `<p class="font-bold text-gray-500 text-xs uppercase tracking-widest mb-1">Sede: ${props.sede || 'No asignada'}</p><p class="font-black text-[#002845] text-xl leading-none">Bloque ${props.bloque || '-'} | Aula ${props.aula || '-'}</p>`;
                    }
                    document.getElementById('info_ubicacion').innerHTML = htmlUbicacion;

                    // 🔥 LÓGICA DE GOOGLE CALENDAR ARREGLADA (Sin puntos ni guiones) 🔥
                    function formatLocalForGoogle(date) {
                        if (!date) return '';
                        let pad = (n) => n < 10 ? '0'+n : n;
                        // Formato YYYYMMDDTHHMMSSZ (Ej: 20260412T080000Z)
                        return date.getUTCFullYear().toString() + pad(date.getUTCMonth() + 1) + pad(date.getUTCDate()) + 'T' + pad(date.getUTCHours()) + pad(date.getUTCMinutes()) + pad(date.getUTCSeconds()) + 'Z';
                    }

                    let startStr = formatLocalForGoogle(info.event.start);
                    let endStr = info.event.end ? formatLocalForGoogle(info.event.end) : formatLocalForGoogle(new Date(info.event.start.getTime() + 60 * 60 * 1000));

                    let tituloUrl = encodeURIComponent("Tutoría: " + info.event.title);
                    let detallesUrl = encodeURIComponent("Reserva generada desde Agenda U.\n¡Recuerda asistir a tu tutoría para no perder el cupo!");
                    let ubicacionUrl = encodeURIComponent(esVirtual ? 'Modalidad Virtual' : `Sede ${props.sede || ''}, Bloque ${props.bloque || ''}, Aula ${props.aula || ''}`);

                    document.getElementById('btnGoogleCalendar').href = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${tituloUrl}&dates=${startStr}/${endStr}&details=${detallesUrl}&location=${ubicacionUrl}`;

                    document.getElementById('formCancelar').action = '/estudiante/reservas/' + info.event.id;
                    document.getElementById('modalDetalle').classList.remove('hidden');
                }
            });
            calendar.render();
        });

        // SWEETALERT PARA CANCELAR RESERVA
        function confirmarCancelacion(btn) {
            Swal.fire({
                title: '¿Estás totalmente seguro?', text: 'Esta acción dejará tu cupo libre para otro estudiante.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Sí, cancelar reserva', cancelButtonText: 'Volver',
                customClass: { title: 'font-extrabold text-[#002845]', popup: 'rounded-3xl shadow-2xl border border-gray-100', confirmButton: 'font-bold px-6 py-2.5 rounded-xl', cancelButton: 'font-bold px-6 py-2.5 rounded-xl text-gray-700 bg-gray-200 hover:bg-gray-300' }
            }).then((result) => { 
                if (result.isConfirmed) { btn.closest('form').submit(); } 
            });
        }
    </script>
    
</body>
</html>