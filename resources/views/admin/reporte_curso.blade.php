{{-- resources/views/admin/reporte_curso.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte por Curso - {{ $curso }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }

        .header { background: #002845; color: white; padding: 20px 28px; margin-bottom: 20px; }
        .header h1 { font-size: 17px; font-weight: bold; }
        .header .curso { font-size: 13px; font-weight: bold; opacity: 0.9; margin-top: 4px; }
        .header p { font-size: 10px; opacity: 0.7; margin-top: 6px; }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #002845;
            border-bottom: 2px solid #002845;
            padding-bottom: 4px;
            margin: 18px 0 10px;
        }

        .stat-row { display: table; width: 100%; margin-bottom: 14px; border-spacing: 0; }
        .stat-box { display: table-cell; width: 25%; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 8px; text-align: center; vertical-align: middle; }
        .stat-box + .stat-box { border-left: none; }
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
        .promedio-general .valor { font-size: 30px; font-weight: bold; }
        .promedio-general .etiq { font-size: 10px; opacity: 0.8; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 16px; }
        th { background: #002845; color: white; padding: 7px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) td { background: #f8fafc; }

        .barra-container { background: #e2e8f0; height: 8px; border-radius: 4px; }
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
    <h1>Reporte por Curso</h1>
    <div class="curso">{{ $curso }}</div>
    <p>Generado: {{ \Carbon\Carbon::now()->locale('es')->isoFormat('DD [de] MMMM [de] YYYY') }}</p>
</div>

<div class="page">

    <div class="section-title">RESUMEN GENERAL</div>
    <div class="stat-row">
        <div class="stat-box">
            <div class="value">{{ $totalInscritos }}</div>
            <div class="label">Total inscritos</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $totalAsistencias }}</div>
            <div class="label">Total asistencias</div>
        </div>
        <div class="stat-box">
            <div class="value">{{ $totalEncuestas }}</div>
            <div class="label">Encuestas respondidas</div>
        </div>
        <div class="stat-box">
            <div class="value">
                {{ $totalInscritos > 0 ? round(($totalAsistencias / $totalInscritos) * 100) : 0 }}%
            </div>
            <div class="label">Tasa de asistencia</div>
        </div>
    </div>

    {{-- SATISFACCIÓN --}}
    @if($totalEncuestas > 0)
        <div class="section-title">SATISFACCIÓN ESTUDIANTIL</div>

        <div class="promedio-general">
            <div class="valor">{{ number_format($promedioGeneral, 1) }} / 5.0</div>
            <div class="etiq">Promedio general de satisfacción ({{ $totalEncuestas }} encuesta(s))</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Criterio</th>
                    <th>Promedio</th>
                    <th style="width:200px">Visualización</th>
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
                        <td><strong>{{ number_format($valor, 1) }}</strong> / 5</td>
                        <td>
                            <div class="barra-container">
                                <div class="barra-fill" style="width: {{ ($valor / 5) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($comentarios->isNotEmpty())
            <div class="section-title">COMENTARIOS DE ESTUDIANTES</div>
            @foreach($comentarios->take(6) as $c)
                <div class="comentarios-box">"{{ $c }}"</div>
            @endforeach
        @endif
    @else
        <p style="color:#94a3b8; font-style:italic; margin:10px 0;">
            No hay encuestas respondidas para este curso todavía.
        </p>
    @endif

    {{-- DETALLE POR GRUPO --}}
    <div class="section-title">DETALLE POR GRUPO / DOCENTE</div>
    <table>
        <thead>
            <tr>
                <th>Docente</th>
                <th>Día</th>
                <th>Horario</th>
                <th>Modalidad</th>
                <th>Sede / Aula</th>
                <th>Inscritos</th>
                <th>Asistencias</th>
                <th>Promedio encuesta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($horarios as $h)
                @php
                    $enc = $h->seguimientos->filter(fn($s) => $s->encuesta)->pluck('encuesta');
                    $prom = $enc->avg('promedio');
                @endphp
                <tr>
                    <td>{{ $h->docente_nombre }}</td>
                    <td>{{ $h->dia_semana }}</td>
                    <td>{{ substr($h->hora_inicio,0,5) }} - {{ substr($h->hora_fin,0,5) }}</td>
                    <td>{{ $h->modalidad }}</td>
                    <td>{{ $h->sede }} {{ $h->aula ? '/ Aula '.$h->aula : '' }}</td>
                    <td>{{ $h->seguimientos->count() }}</td>
                    <td>{{ $h->seguimientos->where('asistencia', true)->count() }}</td>
                    <td>{{ $prom ? number_format($prom, 1).' / 5' : 'Sin datos' }}</td>
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