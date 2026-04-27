<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Asistencia - {{ $horario->curso_nombre }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #002845; padding-bottom: 10px; }
        .title { color: #002845; font-size: 24px; font-weight: bold; margin: 0; }
        .subtitle { font-size: 14px; color: #555; margin-top: 5px; }
        .details-box { background-color: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .details-box p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #002845; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { text-align: center; font-size: 12px; margin-top: 30px; color: #777; border-top: 1px solid #eee; padding-top: 10px;}
    </style>
</head>
<body>

    <div class="header">
        <h1 class="title">I.U. PASCUAL BRAVO</h1>
        <p class="subtitle">Reporte Oficial de Asistencia a Clases de Apoyo</p>
    </div>

    <div class="details-box">
        <p><strong>Curso:</strong> {{ $horario->curso_nombre }}</p>
        <p><strong>Docente:</strong> {{ $horario->docente_nombre }}</p>
        <p><strong>Horario:</strong> {{ $horario->dia_semana }} de {{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</p>
        <p><strong>Ubicación:</strong> {{ $horario->modalidad === 'Virtual' ? 'Virtual' : 'Sede: ' . $horario->sede . ' | Bloque: ' . $horario->bloque . ' | Aula: ' . $horario->aula }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cédula</th>
                <th>Nombre Completo</th>
                <th>Programa Académico</th>
                <th>Firma / Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservas as $index => $reserva)
            <tr>
                <td style="width: 30px; text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $reserva->estudiante->cedula }}</td>
                <td>{{ $reserva->estudiante->nombre_completo }}</td>
                <td>{{ $reserva->estudiante->programa_academico }}</td>
                <td style="width: 120px;">
                    @if($reserva->estado === 'Evaluada')
                        {{ $reserva->asistencia ? 'Asistió' : 'No Asistió' }}
                    @else
                        @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por Agenda U el {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>