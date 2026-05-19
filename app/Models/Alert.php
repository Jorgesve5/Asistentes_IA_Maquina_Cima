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
}
