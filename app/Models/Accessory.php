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
        'delivery_vendors_id', // ✅ thêm vào đây
    ];

    public function category()
    {
        return $this->belongsTo(Category::class)->accessoryType();
    }

    public function deliveryVendor()
    {
        return $this->belongsTo(DeliveryVendor::class, 'delivery_vendors_id');
    }
}

