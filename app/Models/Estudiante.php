<?php

// app/Models/Estudiante.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    // Campos que permitimos guardar masivamente
    protected $fillable = [
        'cedula',
        'nombre_completo',
        'programa_academico',
        'esta_activo',
    ];

    // Relación: Un estudiante tiene muchos seguimientos
    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class);
    }
}