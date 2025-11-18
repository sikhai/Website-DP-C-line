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

    protected $appends = ['image'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'parent_id');
    }

    /**
     * Tổng số products thuộc một design.
     *
     * @return int
     */
    public function getTotalProductsAttribute()
    {
        // Nếu quan hệ đã load: count() không tốn query
        // Nếu chưa load: Eloquent tự chạy SELECT COUNT(*) WHERE design_id = ?
        return $this->products()->count();
    }

    /**
     * Virtual attribute: image
     * Nếu design chưa có image, lấy image của product đầu tiên
     */
    public function getImageAttribute()
    {
        // Nếu bạn muốn có field image trong design, gán giá trị từ product đầu tiên
        $firstProduct = $this->products()->whereNotNull('image')->first();

        return $firstProduct ? $firstProduct->image : null;
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
