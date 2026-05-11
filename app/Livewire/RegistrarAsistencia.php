<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seguimiento;

class RegistrarAsistencia extends Component
{
    public int $horarioId;
    public int $estudianteId;
    public $asistenciaRegistrada = false;

    public function registrar()
    {
        // Guardamos el registro en la base de datos
        Seguimiento::create([
            'estudiante_id' => $this->estudianteId,
            'horario_id' => $this->horarioId,
            'fecha' => now()->toDateString(), // Fecha actual
            'hora_registro' => now()->toTimeString(), // Hora exacta del clic
            'estado' => 'Asistió'
        ]);

        // Cambiamos el estado para actualizar el botón en pantalla
        $this->asistenciaRegistrada = true;
    }

    public function render()
    {
        return view('livewire.registrar-asistencia');
    }
}