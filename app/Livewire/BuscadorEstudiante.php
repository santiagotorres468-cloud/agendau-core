<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Estudiante;
use App\Models\HorarioAsesoria;
use App\Models\Seguimiento;
use Carbon\Carbon;

class BuscadorEstudiante extends Component
{
    public $cedula = '';
    public $estudiante = null;
    public $horarios = [];

    public function updatedCedula()
    {
        if (strlen($this->cedula) >= 5) {
            $this->estudiante = Estudiante::where('cedula', $this->cedula)
                                          ->where('esta_activo', true)
                                          ->first();

            if ($this->estudiante) {
                $this->horarios = HorarioAsesoria::all();
            } else {
                $this->horarios = [];
            }
        } else {
            $this->estudiante = null;
            $this->horarios = [];
        }
    }

    public function reservarCupo($horarioId)
    {
        $horario = HorarioAsesoria::find($horarioId);
        
        $dias = ['Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday', 'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday'];
        $diaIngles = $dias[$horario->dia_semana] ?? 'Monday';
        $fechaExactaDB = Carbon::parse('next ' . $diaIngles)->format('Y-m-d');

        // Guardamos la reserva en la tabla seguimientos
        Seguimiento::create([
            'estudiante_id' => $this->estudiante->id,
            'horario_id' => $horario->id,
            'fecha' => $fechaExactaDB,
            'estado' => 'Programada',
            'hora_registro' => now()->toTimeString() 
        ]);

        session()->flash('mensaje_reserva', '¡Cupo reservado con éxito para el próximo ' . $horario->dia_semana . '!');
    }

    public function calcularProximaFecha($diaEspanol)
    {
        $dias = ['Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday', 'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday'];
        $diaIngles = $dias[$diaEspanol] ?? 'Monday';
        return Carbon::parse('next ' . $diaIngles)->locale('es')->isoFormat('dddd, DD \d\e MMMM');
    }

    public function render()
    {
        return view('livewire.buscador-estudiante');
    }
}