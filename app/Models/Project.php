<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'product_descriptions',
        'images',
        'images_with_captions',
        'slug',
        'is_featured'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_project');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     // Lắng nghe sự kiện `saving` (gọi cả khi tạo và cập nhật)
    //     static::saving(function ($category) {
    //         dd(request()->all()); // Dừng và in dữ liệu của `Category` trước khi lưu
    //         // dd($Category); // Dừng và in dữ liệu của `Category` trước khi lưu
    //     });
    // }
}
