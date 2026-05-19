<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisorMessage extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'machine_id', 'machine_name', 'text', 'from', 'senderName', 'timestamp', 'read'
    ];

    protected $casts = [
        'read' => 'boolean',
    ];
}
