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

    public function childCategories()
    {
        return $this->hasMany(Design::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Lắng nghe sự kiện `saving` (gọi cả khi tạo và cập nhật)
        static::saving(function ($category) {
            // dd(request()->all()); // Dừng và in dữ liệu của `Category` trước khi lưu
            // dd($Category); // Dừng và in dữ liệu của `Category` trước khi lưu
        });
    }
}
