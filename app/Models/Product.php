<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;
use TCG\Voyager\Facades\Voyager;
use App\Models\Design;

class Product extends Model
{
    use HasFactory, Translatable;

    protected $casts = [
        'images' => 'array',
    ];

    protected $fillable = [
        'name', 'title', 'product_code', 'description', 'short_description', 'price', 'meter', 'tax', 'stock_quantity',
        'image', 'images', 'keywords', 'slug', 'status', 'is_featured', 'category_id'
    ];

    protected $translatable = ['name', 'short_description', 'description', 'keywords', 'slug'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->belongsTo(Design::class, 'category_id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attribute', 'product_id', 'attribute_id')
                    ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'product_project');
    }
}

