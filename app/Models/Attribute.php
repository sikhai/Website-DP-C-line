<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'value', 'type', 'status'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    // helper to get attribute id quickly
    public static function getIdByName(string $name)
    {
        return static::where('name', $name)->value('id');
    }
}

