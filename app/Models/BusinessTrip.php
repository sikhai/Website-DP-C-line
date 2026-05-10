<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessTrip extends Model
{
    protected $fillable = [
        'user_id',
        'payroll_id',
        'title',
        'destination',
        'start_date',
        'end_date',
        'estimated_amount',
        'total_advanced_amount',
        'actual_spent_amount',
        'difference_amount',
        'status',
        'employee_confirmed_at',
        'accountant_confirmed_at',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'estimated_amount' => 'decimal:2',
        'total_advanced_amount' => 'decimal:2',
        'actual_spent_amount' => 'decimal:2',
        'difference_amount' => 'decimal:2',
        'employee_confirmed_at' => 'datetime',
        'accountant_confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function advances()
    {
        return $this->hasMany(BusinessTripAdvance::class);
    }
}
