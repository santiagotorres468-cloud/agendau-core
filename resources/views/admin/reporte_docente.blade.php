<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Docente</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .header { background-color: #002845; color: white; padding: 20px; text-align: center; border-radius: 8px; }
        h1 { margin: 0; font-size: 24px; }
        .info { margin: 20px 0; padding: 15px; background-color: #f8fafc; border-left: 5px solid #10b981; }
        table { w-full; border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #002845; color: white; font-size: 12px; text-transform: uppercase; }
        .success { color: #10b981; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Asesorías Brindadas</h1>
        <p>Docente: {{ $docente->name }}</p>
    </div>

    <div class="info">
        <p><strong>Email:</strong> {{ $docente->email }}</p>
        <p><strong>Total de clases asignadas:</strong> {{ $horarios->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th>Día y Hora</th>
                <th>Estudiantes Asistentes</th>
                <th>Modalidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach($horarios as $clase)
            <tr>
                <td><strong>{{ $clase->curso_nombre }}</strong></td>
                <td>{{ $clase->dia_semana }} ({{ \Carbon\Carbon::parse($clase->hora_inicio)->format('H:i') }})</td>
                <td class="success">{{ $clase->seguimientos->count() }} asistentes</td>
                <td>{{ $clase->modalidad }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>