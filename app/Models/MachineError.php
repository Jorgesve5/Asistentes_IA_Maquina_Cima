<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineError extends Model
{
    protected $fillable = [
        'machine_id', 'user_message', 'image_path', 'ai_response'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
