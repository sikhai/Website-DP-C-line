<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $fillable = [
        'extra_key_id',
        'extra_value_id'
    ];

    public function key()
    {
        return $this->belongsTo(ExtraKey::class,'extra_key_id');
    }

    public function value()
    {
        return $this->belongsTo(ExtraValue::class,'extra_value_id');
    }

    public function extraable()
    {
        return $this->morphTo();
    }
}