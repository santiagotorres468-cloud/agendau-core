<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $fillable = [
        'seguimiento_id',
        'estudiante_id',
        'horario_id',
        'p1_claridad',
        'p2_puntualidad',
        'p3_dominio_tema',
        'p4_utilidad',
        'p5_ambiente',
        'resumen_sesion',
        'aspectos_mejorar',
        'comentario',
        'promedio',
    ];

    protected function casts(): array
    {
        return [
            'p1_claridad'     => 'integer',
            'p2_puntualidad'  => 'integer',
            'p3_dominio_tema' => 'integer',
            'p4_utilidad'     => 'integer',
            'p5_ambiente'     => 'integer',
            'promedio'        => 'decimal:2',
        ];
    }

    // ─────────────────────────────────────────────
    // RELACIONES
    // ─────────────────────────────────────────────

    public function seguimiento()
    {
        return $this->belongsTo(Seguimiento::class);
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function horario()
    {
        return $this->belongsTo(HorarioAsesoria::class, 'horario_id');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Calcula y guarda el promedio antes de guardar.
     */
    public static function calcularPromedio(array $data): float
    {
        $puntajes = [
            $data['p1_claridad']    ?? 0,
            $data['p2_puntualidad'] ?? 0,
            $data['p3_dominio_tema'] ?? 0,
            $data['p4_utilidad']    ?? 0,
            $data['p5_ambiente']    ?? 0,
        ];
        return round(array_sum($puntajes) / count($puntajes), 2);
    }

    /**
     * Etiqueta textual para un puntaje 1-5
     */
    public static function etiqueta(int $puntaje): string
    {
        return match($puntaje) {
            1 => 'Muy malo',
            2 => 'Malo',
            3 => 'Regular',
            4 => 'Bueno',
            5 => 'Excelente',
            default => 'Sin calificar',
        };
    }
}