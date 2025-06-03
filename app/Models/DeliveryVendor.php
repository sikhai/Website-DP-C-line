<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryVendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'point_of_origin',
    ];

    public function freightRates()
    {
        return $this->hasMany(DeliveryFreightRate::class, 'vendor_id');
    }
}
