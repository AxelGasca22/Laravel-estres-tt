<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SesionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $wasUpdated = $this->updated_at && $this->created_at
            ? $this->updated_at->gt($this->created_at)
            : false;

        $status = $this->trashed()
            ? 'cancelada'
            : ($wasUpdated ? 'actualizada' : 'programada');

        return [
            'id' => $this->id,
            'fecha' => $this->fecha,
            'hora' => $this->hora ? Carbon::parse($this->hora)->format('H:i') : null,
            'tipo_sesion' => $this->tipo_sesion,
            'observaciones' => $this->observaciones,
            'estado' => $status,
            'fue_actualizada' => $wasUpdated && !$this->trashed(),
            'fue_cancelada' => $this->trashed(),
            'ultima_modificacion' => $this->updated_at?->toIso8601String(),
            'creada_en' => $this->created_at?->toIso8601String(),
            'psicologo' => $this->psicologo ? [
                'id' => $this->psicologo->id,
                'name' => $this->psicologo->user?->name,
            ] : null,
            'paciente' => $this->paciente ? [
                'id' => $this->paciente->id,
                'name' => $this->paciente->user?->name,
            ] : null,
        ];
    }
}
