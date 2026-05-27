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
        return '';
    }
}
