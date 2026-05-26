<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedConversation extends Model
{
    protected $fillable = [
        'machine_id',
        'title',
        'description',
        'messages'
    ];

    protected $casts = [
        'messages' => 'array',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
