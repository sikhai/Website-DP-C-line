<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Product extends Model
{
    use HasFactory, Translatable;

    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'title',
        'code',
        'slug',
        'short_description',
        'description',
        'price',
        'stock_quantity',
        'image',
        'keywords',
        'status',
        'is_featured',
    ];

    protected $translatable = ['name', 'short_description', 'description', 'keywords', 'slug'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute_values', 'product_id', 'attribute_id')
                    ->withPivot('Value');
    }

    public function setIsFeaturedAttribute($value)
    {
        $this->attributes['is_featured'] = $value ? 1 : 0;
    }
}
