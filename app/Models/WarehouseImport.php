<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseImport extends Model
{
    protected $fillable = [
        'product_id',
        'weight',
        'import_code',
        'user_id',
        'project_id',
        'more_info',
    ];

    protected $casts = [
        'more_info' => 'array', // để Laravel tự decode JSON thành array
    ];

    // Sản phẩm được nhập
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Người dùng thực hiện nhập kho
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Dự án liên quan
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
