<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryFreightType extends Model
{
    protected $table = 'delivery_freight_types';

    protected $fillable = [
        'type',
        'name',
    ];

    public function freightRates()
    {
        return $this->hasMany(DeliveryFreightRate::class, 'freight_type_id');
    }
}
