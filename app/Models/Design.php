<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Models\Category;
use App\Models\Collection;

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
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Lắng nghe sự kiện `saving` (gọi cả khi tạo và cập nhật)
        static::saving(function ($design) {
            // dd(request()->all()); // Dừng và in dữ liệu của `Design` trước khi lưu
            // dd($design); // Dừng và in dữ liệu của `Design` trước khi lưu
        });
    }
}
