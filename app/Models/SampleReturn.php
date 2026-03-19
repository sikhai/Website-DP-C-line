<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SampleReturn extends Model
{
    protected $fillable = [
        'sample_request_id',
        'quantity',
        'warehouse_confirmed',
        'confirmed_at',
    ];

    protected $casts = [
        'sample_request_id' => 'integer',
        'quantity' => 'integer',
        'warehouse_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function sampleRequest()
    {
        return $this->belongsTo(SampleRequest::class);
    }

    // tiện dùng: lấy luôn sample
    public function sample()
    {
        return $this->hasOneThrough(
            Sample::class,
            SampleRequest::class,
            'id',
            'id',
            'sample_request_id',
            'sample_id'
        );
    }
}