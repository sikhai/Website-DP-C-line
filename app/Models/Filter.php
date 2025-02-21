<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    protected $fillable = [
        'name',
        'attributes_value',
        'status',
    ];

    public function setAttributesValueAttribute($value)
    {
        // Chuyển mảng thành chuỗi JSON trước khi lưu
        $this->attributes['attributes_value'] = is_array($value) ? json_encode($value) : $value;
    }
}
