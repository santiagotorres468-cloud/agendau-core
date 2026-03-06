<?php

// app/Models/HorarioAsesoria.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HorarioAsesoria extends Model
{
    // Le indicamos explícitamente el nombre de la tabla para evitar que 
    // Laravel la busque en inglés (horario_asesorias)
    protected $table = 'horarios_asesoria';

    protected $fillable = [
        'user_id',
        'curso_nombre',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'docente_nombre', // <- ¡Esta era la que bloqueaba todo!
        'lugar',
        'semestre',       // <- Aseguramos esta también
        'modalidad',
        'sede',
        'bloque',
        'aula'
    ];

    // Relación: Un horario tiene muchos seguimientos
    public function seguimientos(): HasMany
    {
        // Pasamos el foreign_key personalizado porque no es 'horario_asesoria_id'
        return $this->hasMany(Seguimiento::class, 'horario_id');
    }
}
