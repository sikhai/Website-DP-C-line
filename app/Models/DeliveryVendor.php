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
        'attributes'
    ];

    protected $casts = [
        'attributes' => 'array',
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

    public function getAttributeItemsAttribute()
    {
        if (!$this->attributes['attributes']) return [];

        $items = collect(json_decode($this->attributes['attributes'], true));

        // Nếu có value là id của Accessory, ta có thể eager load
        $accessoryIds = $items->pluck('value')->filter()->all();

        $accessories = Accessory::whereIn('id', $accessoryIds)->get()->keyBy('id');

        return $items->map(function ($item) use ($accessories) {
            $accessory = $accessories->get($item['value']);
            return [
                'name' => $item['name'],
                'value' => $accessory ? $accessory->name : $item['value'],
                'accessory' => $accessory,
            ];
        });
    }
}
