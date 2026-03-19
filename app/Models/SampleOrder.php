<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\SampleOrderStatus;

class SampleOrder extends Model
{
    protected $fillable = [
        'sample_id',
        'quantity',
        'expected_date',
        'received_date',
        'status' => SampleOrderStatus::class,
        'note',
    ];

    protected $casts = [
        'sample_id' => 'integer',
        'quantity' => 'integer',
        'expected_date' => 'date',
        'received_date' => 'date',
    ];

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}