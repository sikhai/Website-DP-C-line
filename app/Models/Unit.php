<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(WarehouseProductTransaction::class);
    }
}
