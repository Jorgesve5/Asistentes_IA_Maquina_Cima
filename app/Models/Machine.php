<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'status', 'serial', 'indicator', 'column', 'row', 'subLabel', 'custom_prompt'
    ];

    public function manuals()
    {
        return $this->hasMany(Manual::class);
    }
}
