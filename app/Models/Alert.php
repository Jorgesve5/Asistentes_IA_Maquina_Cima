<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'machine_id', 'machine_name', 'message', 'type', 'timestamp', 'read'
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function getCleanStateAttribute()
    {
        $labels = [
            'info' => 'Disponible',
            'warning' => 'Avería',
            'maintenance' => 'Mantenimiento',
            'waiting' => 'En Espera',
        ];
        return $labels[$this->type] ?? 'Desconocido';
    }

    public function getCleanDescriptionAttribute()
    {
        if (str_contains($this->message, '. Motivo: ')) {
            $parts = explode('. Motivo: ', $this->message);
            return trim($parts[1]);
        }
        if (str_contains($this->message, 'por el Supervisor desde el Chat.')) {
            return 'Cambiado por el Supervisor desde el Chat.';
        }
        if (str_contains($this->message, '. Observaciones: ')) {
            $parts = explode('. Observaciones: ', $this->message);
            return trim($parts[1]);
        }
        return '';
    }

    public function getElapsedTimeAttribute()
    {
        if (str_contains($this->message, 'Tiempo transcurrido: ')) {
            $parts = explode('Tiempo transcurrido: ', $this->message);
            $timePart = $parts[1];
            if (str_contains($timePart, '. Observaciones: ')) {
                $subParts = explode('. Observaciones: ', $timePart);
                return trim($subParts[0]);
            }
            return trim($timePart);
        }
        return null;
    }
}
