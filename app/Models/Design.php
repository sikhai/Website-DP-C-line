<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Category;

class Design extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'images',
        'description',
        'is_featured',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
 
}
