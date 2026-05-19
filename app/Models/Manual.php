<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    protected $fillable = [
        'machine_id', 'fileName', 'size', 'text'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
