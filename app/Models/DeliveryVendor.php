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
        'type',
    ];

    public function freightRates()
    {
        return $this->hasMany(DeliveryFreightRate::class, 'vendor_id');
    }
    // 🔹 Helper scopes
    public function scopeDelivery($query)
    {
        return $query->where('type', 'Delivery');
    }

    public function scopeAccessories($query)
    {
        return $query->where('type', 'Accessories');
    }
}
