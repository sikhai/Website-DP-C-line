<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTripAdvance extends Model
{
    protected $fillable = [
        'business_trip_id',
        'amount',
        'confirmed_by',
        'confirmed_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
