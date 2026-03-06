<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    // Aquí le damos permiso a TODOS los campos, incluyendo hora_registro
    protected $fillable = [
    'horario_id',
    'estudiante_id',
    'fecha',
    'hora_registro',
    'estado',
    'asistencia',
    'evolucion', // <-- ¡AQUÍ ESTÁ EL PERMISO NUEVO!
   ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function horario()
    {
        return $this->belongsTo(HorarioAsesoria::class, 'horario_id');
    }
}