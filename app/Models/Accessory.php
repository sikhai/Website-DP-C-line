<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessory extends Model
{
    use HasFactory;

    protected $table = 'accessories';

    protected $fillable = [
        'name',
        'category_id',
        'product_code',
        'price',
        'dealer_price',
        'stock_quantity',
        'measure',
        'image',
        'slug',
        'description',
        'short_description',
        'keywords',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class)->accessoryType();
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
