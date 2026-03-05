<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraKey extends Model
{
    protected $fillable = ['name'];

    public function values()
    {
        return $this->hasMany(ExtraValue::class);
    }
}