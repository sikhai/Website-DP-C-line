<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraValue extends Model
{
    protected $fillable = [
        'extra_key_id',
        'value'
    ];

    public function key()
    {
        return $this->belongsTo(ExtraKey::class,'extra_key_id');
    }
}