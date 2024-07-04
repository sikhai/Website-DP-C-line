<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Facades\Voyager;

class Product extends Model
{
    use HasFactory, Translatable;

    protected $fillable = [
        'product_name', 'title', 'product_code', 'description', 'short_description', 'price', 'stock_quantity',
        'image', 'keywords', 'slug', 'status', 'is_featured', 'category_id'
    ];

    protected $translatable = ['product_name', 'short_description', 'description', 'keywords', 'slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Voyager::modelClass('Category'));
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute', 'product_id', 'attribute_id')
                    ->withTimestamps();
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = $value ? 1 : 0;
    }
}

