<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Category;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'shape',
        'image',
        'images',
        'description',
        'is_featured',
        'parent_id',
    ];

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id')->productType();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id')->productType();
    }

    public function designs()
    {
        return $this->hasMany(Design::class, 'parent_id');
    }

    /**
     * Tổng số products của tất cả designs trong collection.
     * @return int
     */
    public function getTotalProductsAttribute()
    {
        // Gộp toàn bộ products của tất cả designs, sau đó đếm tổng
        return $this->designs->flatMap->products->count();
    }
}
