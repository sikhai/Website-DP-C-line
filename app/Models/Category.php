<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Design;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'shape',
        'image',
        'images',
        'description',
        'is_featured',
        'parent_id'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Design::class, 'parent_id');
    }
    

    // public function childCategories()
    // {
    //     return $this->hasMany(Design::class, 'parent_id');
    // }

    // public function scopeExcludeSelf($query)
    // {
    //     return $query->where('id', '!=', $this->id);
    // }  
}
