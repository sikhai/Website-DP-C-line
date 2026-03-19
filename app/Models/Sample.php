<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $table = 'samples';

    /**
     * Các field cho phép mass assign
     */
    protected $fillable = [
        'code',
        'name',
        'delivery_vendors_id',
        'type',
    ];

    /**
     * Cast dữ liệu (nếu cần)
     */
    protected $casts = [
        'id' => 'integer',
        'delivery_vendors_id' => 'integer',
    ];

    /**
     * Relationship: Sample thuộc về DeliveryVendor
     */
    public function deliveryVendor()
    {
        return $this->belongsTo(DeliveryVendor::class, 'delivery_vendors_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        do {
            $code = 'SMP-' . strtoupper(uniqid());
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function orders()
    {
        return $this->hasMany(SampleOrder::class);
    }

    public function requests()
    {
        return $this->hasMany(SampleRequest::class);
    }

    public function transactions()
    {
        return $this->hasMany(SampleTransaction::class);
    }
}
