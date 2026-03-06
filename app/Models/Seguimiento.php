<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    // Aquí le damos permiso a TODOS los campos, incluyendo hora_registro
    protected $fillable = [
        'estudiante_id',
        'horario_id',
        'fecha',
        'estado',
        'asistencia',
        'observaciones',
        'hora_registro'
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