<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evolución - Agenda U</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; font-size: 14px; }
        .header { text-align: center; border-bottom: 3px solid #002845; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { color: #002845; margin: 0; font-size: 26px; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 5px 0 0; color: #666; font-size: 13px; font-weight: bold; }
        
        .section-title { font-size: 15px; color: #002845; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-weight: bold; text-transform: uppercase; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 6px 0; vertical-align: top; }
        .info-label { font-weight: bold; width: 160px; color: #4b5563; }
        .info-data { color: #111827; }

        .evolucion-box { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 6px; font-style: italic; color: #1e293b; margin-bottom: 25px; }
        
        /* NUEVO: Estilos para el conteo de asistencia */
        .stats-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-bottom: 20px; }
        .stat-box { background-color: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; text-align: center; width: 33%; }
        .stat-title { font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .stat-value { font-size: 22px; font-weight: bold; margin-top: 5px; }
        .val-total { color: #002845; }
        .val-presente { color: #16a34a; }
        .val-ausente { color: #dc2626; }

        .history-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .history-table th { background-color: #002845; color: #ffffff; padding: 12px; text-align: left; font-size: 13px; text-transform: uppercase; }
        .history-table td { border-bottom: 1px solid #e2e8f0; padding: 12px; font-size: 13px; }
        
        .status-presente { color: #16a34a; font-weight: bold; }
        .status-ausente { color: #dc2626; font-weight: bold; }
        .status-pendiente { color: #64748b; font-style: italic; }

        .footer { text-align: center; margin-top: 50px; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Agenda U</h1>
        <p>REPORTE OFICIAL DE SEGUIMIENTO Y EVOLUCIÓN ACADÉMICA</p>
    </div>

    <div class="section-title">Datos del Estudiante</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nombre Completo:</td>
            <td class="info-data">{{ $reserva->estudiante->nombre_completo }}</td>
        </tr>
        <tr>
            <td class="info-label">Documento de Identidad:</td>
            <td class="info-data">{{ $reserva->estudiante->cedula }}</td>
        </tr>
    </table>

    <div class="section-title">Detalles de la Asesoría</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Curso Asignado:</td>
            <td class="info-data">{{ $reserva->horario->curso_nombre }}</td>
        </tr>
        <tr>
            <td class="info-label">Docente a Cargo:</td>
            <td class="info-data">{{ $reserva->horario->docente_nombre }}</td>
        </tr>
        <tr>
            <td class="info-label">Fecha de Emisión:</td>
            <td class="info-data">{{ \Carbon\Carbon::now()->format('d/m/Y - h:i A') }}</td>
        </tr>
    </table>

    <div class="section-title">Observaciones del Docente</div>
    <div class="evolucion-box">
        "{{ $reserva->evolucion ?? 'No se registraron observaciones adicionales en este reporte.' }}"
    </div>

    @php
        $totalReservas = $historial->count();
        $totalAsistidas = $historial->where('asistencia', 1)->count();
        $totalFaltas = $historial->where('asistencia', 0)->whereNotNull('asistencia')->count();
    @endphp

    <div class="section-title">Resumen de Asistencia del Curso</div>
    <table class="stats-table">
        <tr>
            <td class="stat-box">
                <div class="stat-title">Clases Reservadas</div>
                <div class="stat-value val-total">{{ $totalReservas }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-title">Asistencias Confirmadas</div>
                <div class="stat-value val-presente">{{ $totalAsistidas }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-title">Inasistencias</div>
                <div class="stat-value val-ausente">{{ $totalFaltas }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Historial Detallado</div>
    <table class="history-table">
        <thead>
            <tr>
                <th>Fecha de la Asesoría</th>
                <th>Estado de Asistencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($historial as $registro)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                    <td>
                        @if($registro->estado === 'Programada')
                            <span class="status-pendiente">Pendiente por Evaluar</span>
                        @elseif($registro->asistencia === 1)
                            <span class="status-presente">Presente</span>
                        @elseif($registro->asistencia === 0)
                            <span class="status-ausente">Ausente</span>
                        @else
                            <span class="status-pendiente">Sin Registro</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Este documento es generado automáticamente por el sistema de gestión académica Agenda U.<br>
        Las observaciones plasmadas son responsabilidad exclusiva del docente titular.<br>
        © {{ date('Y') }} Todos los derechos reservados.
    </div>

</body>
</html>