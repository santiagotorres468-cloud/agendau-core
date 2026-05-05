{{-- resources/views/admin/reporte_docente.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Docente - {{ $docente->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

        .header {
            background: #002845;
            color: white;
            padding: 20px 28px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 10px; opacity: 0.75; margin-top: 4px; }
        .header .meta { margin-top: 10px; font-size: 10px; opacity: 0.85; }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #002845;
            border-bottom: 2px solid #002845;
            padding-bottom: 4px;
            margin: 18px 0 10px;
        }

        .stat-row {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border-spacing: 0;
        }
        .stat-box {
            display: table-cell;
            width: 25%;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 8px;
            text-align: center;
            vertical-align: middle;
        }
        .stat-box + .stat-box {
            border-left: none;
        }
        .stat-box .value { font-size: 20px; font-weight: bold; color: #002845; }
        .stat-box .label { font-size: 9px; color: #64748b; margin-top: 2px; }

        .promedio-general {
            background: #002845;
            color: white;
            border-radius: 8px;
            padding: 14px 20px;
            text-align: center;
            margin-bottom: 16px;
        }
        .promedio-general .valor { font-size: 32px; font-weight: bold; }
        .promedio-general .etiq { font-size: 10px; opacity: 0.8; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 16px;
        }
        th {
            background: #002845;
            color: white;
            padding: 7px 8px;
            text-align: left;
        }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) td { background: #f8fafc; }

        .barra-container { background: #e2e8f0; height: 8px; border-radius: 4px; width: 100%; }
        .barra-fill { background: #002845; height: 8px; border-radius: 4px; }

        .comentarios-box {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f8fafc;
            margin-bottom: 6px;
            font-size: 10px;
            color: #374151;
        }
        .comentarios-box::before {
            content: '"';
            font-size: 18px;
            color: #002845;
            line-height: 1;
        }

        .footer {
            margin-top: 28px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }

        .page { padding: 0 28px 28px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Reporte de Desempeño Docente</h1>
    <p>Sistema de Monitoreo de Tutorías y Asesorías</p>
    <div class="meta">
        Docente: <strong>{{ $docente->name }}</strong> &nbsp;|&nbsp;
        Generado: {{ \Carbon\Carbon::now()->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}
    </div>
</div>

<div class="page">

    {{-- RESUMEN ESTADÍSTICO --}}
    <div class="section-title">RESUMEN GENERAL</div>
    <div class="stat-row">
        <div class="stat-box">
            <div class="value">{{ $totalSesiones }}</div>
            <div class="label">Total sesiones</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $totalAsistencias }}</div>
            <div class="label">Asistencias confirmadas</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $totalEncuestas }}</div>
            <div class="label">Encuestas respondidas</div>
        </div>
        <div class="stat-box">
            <div class="value">
                {{ $totalSesiones > 0 ? round(($totalAsistencias / $totalSesiones) * 100) : 0 }}%
            </div>
            <div class="label">Tasa de asistencia</div>
        </div>
    </div>

    {{-- CALIFICACIÓN GENERAL --}}
    @if($totalEncuestas > 0)
        <div class="section-title">CALIFICACIÓN DE SATISFACCIÓN</div>

        <div class="promedio-general">
            <div class="valor">{{ number_format($promedioGeneral, 1) }} / 5.0</div>
            <div class="etiq">Promedio general de satisfacción estudiantil ({{ $totalEncuestas }} encuesta(s))</div>
        </div>

        {{-- Barras por criterio --}}
        <table>
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th style="text-align:center">Promedio</th>
                    <th style="width:200px">Calificación visual</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $criterios = [
                        'Claridad en las explicaciones' => $promedioClaro,
                        'Puntualidad y cumplimiento'    => $promedioPuntual,
                        'Dominio del tema'              => $promedioDominio,
                        'Utilidad de la sesión'         => $promedioUtilidad,
                        'Ambiente y condiciones'        => $promedioAmbiente,
                    ];
                @endphp
                @foreach($criterios as $nombre => $valor)
                    <tr>
                        <td>{{ $nombre }}</td>
                        <td style="text-align:center"><strong>{{ number_format($valor, 1) }}</strong> / 5</td>
                        <td>
                            <div class="barra-container">
                                <div class="barra-fill" style="width: {{ ($valor / 5) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Comentarios --}}
        @if($comentarios->isNotEmpty())
            <div class="section-title">COMENTARIOS ESTUDIANTILES</div>
            @foreach($comentarios->take(8) as $comentario)
                <div class="comentarios-box">{{ $comentario }}"</div>
            @endforeach
        @endif
    @else
        <p style="color:#94a3b8; font-style:italic; margin: 10px 0;">
            Aún no hay encuestas respondidas para este docente.
        </p>
    @endif

    {{-- DETALLE POR HORARIO --}}
    <div class="section-title">DETALLE DE SESIONES</div>
    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Día</th>
                <th>Horario</th>
                <th>Modalidad</th>
                <th>Inscritos</th>
                <th>Asistencias</th>
                <th>Encuestas</th>
                <th>Promedio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($horarios as $h)
                @php
                    $enc = $h->seguimientos->filter(fn($s) => $s->encuesta)->pluck('encuesta');
                    $prom = $enc->avg('promedio');
                @endphp
                <tr>
                    <td>{{ $h->curso_nombre }}</td>
                    <td>{{ $h->dia_semana }}</td>
                    <td>{{ substr($h->hora_inicio,0,5) }} - {{ substr($h->hora_fin,0,5) }}</td>
                    <td>{{ $h->modalidad }}</td>
                    <td>{{ $h->seguimientos->count() }}</td>
                    <td>{{ $h->seguimientos->where('asistencia', true)->count() }}</td>
                    <td>{{ $enc->count() }}</td>
                    <td>{{ $prom ? number_format($prom, 1) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Asesorías — Institución Universitaria Pascual Bravo
    </div>

</div>
</body>
</html>