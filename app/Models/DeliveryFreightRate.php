<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryFreightRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'freight_type_id',
        'weight_from',
        'weight_to',
        'rate',
    ];

    public function vendor()
    {
        return $this->belongsTo(DeliveryVendor::class, 'vendor_id');
    }

    public function freightType()
    {
        return $this->belongsTo(DeliveryFreightType::class, 'freight_type_id');
    }
}
