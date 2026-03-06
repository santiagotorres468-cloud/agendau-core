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
    public $misReservas = []; // Nueva variable para mostrar los seguimientos

    // Refrescamos los datos de tablas
    public function cargarDatos()
    {
        if ($this->estudiante) {
            $this->horarios = HorarioAsesoria::all();
            
            // Traemos las reservas activas de este estudiante
            $this->misReservas = Seguimiento::where('estudiante_id', $this->estudiante->id)
                                            ->where('estado', 'Programada')
                                            ->with('horario') // Traemos la info de la clase
                                            ->get();
        } else {
            $this->horarios = [];
            $this->misReservas = [];
        }
    }

    public function updatedCedula()
    {
        if (strlen($this->cedula) >= 5) {
            $this->estudiante = Estudiante::where('cedula', $this->cedula)
                                          ->where('esta_activo', true)
                                          ->first();
            $this->cargarDatos();
        } else {
            $this->estudiante = null;
            $this->cargarDatos();
        }
    }

    public function reservarCupo($horarioId)
    {
        // Candado de seguridad: Evitar duplicados
        $existe = Seguimiento::where('estudiante_id', $this->estudiante->id)
                             ->where('horario_id', $horarioId)
                             ->where('estado', 'Programada')
                             ->exists();

        if ($existe) {
            session()->flash('error_reserva', '⚠️ Ya tienes una reserva activa para esta clase.');
            return;
        }

        $horario = HorarioAsesoria::find($horarioId);
        
        $dias = ['Lunes' => 'Monday', 'Martes' => 'Tuesday', 'Miércoles' => 'Wednesday', 'Jueves' => 'Thursday', 'Viernes' => 'Friday', 'Sábado' => 'Saturday'];
        $diaIngles = $dias[$horario->dia_semana] ?? 'Monday';
        $fechaExactaDB = Carbon::parse('next ' . $diaIngles)->format('Y-m-d');

        Seguimiento::create([
            'estudiante_id' => $this->estudiante->id,
            'horario_id' => $horario->id,
            'fecha' => $fechaExactaDB,
            'estado' => 'Programada',
            'hora_registro' => now()->toTimeString() 
        ]);

        session()->flash('mensaje_reserva', '✅ ¡Cupo reservado con éxito para el ' . $horario->dia_semana . '!');
        $this->cargarDatos(); // Recargamos la tabla de Mis Reservas
    }

    // NUEVA FUNCIÓN: Cancelar la reserva
    public function cancelarReserva($seguimientoId)
    {
        $reserva = Seguimiento::where('id', $seguimientoId)
                              ->where('estudiante_id', $this->estudiante->id)
                              ->first();

        if ($reserva) {
            $reserva->delete(); // Eliminamos el registro
            session()->flash('mensaje_reserva', '🗑️ Tu reserva ha sido cancelada exitosamente.');
            $this->cargarDatos(); // Actualizamos la vista
        }
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