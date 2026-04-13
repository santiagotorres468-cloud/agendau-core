<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Curso</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .header { background-color: #059669; color: white; padding: 20px; text-align: center; border-radius: 8px; }
        h1 { margin: 0; font-size: 24px; }
        .stats { display: flex; margin-top: 20px; }
        .stat-box { padding: 15px; background: #f0fdf4; border: 1px solid #10b981; margin-right: 10px; border-radius: 8px; width: 45%; }
        table { w-full; border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #059669; color: white; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte Global del Curso</h1>
        <p>{{ $curso }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>👥 Total Inscripciones</h3>
            <p style="font-size: 24px; font-weight: bold; color: #002845;">{{ $totalInscritos }}</p>
        </div>
        <div class="stat-box">
            <h3>✅ Asistencias Confirmadas</h3>
            <p style="font-size: 24px; font-weight: bold; color: #059669;">{{ $totalAsistencias }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Docente a cargo</th>
                <th>Día y Hora</th>
                <th>Modalidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($horarios as $clase)
            <tr>
                <td>{{ $clase->docente_nombre }}</td>
                <td>{{ $clase->dia_semana }} a las {{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }}</td>
                <td>{{ $clase->modalidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>