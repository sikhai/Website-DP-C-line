<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollOvertime extends Model
{
    protected $fillable = [
        'payroll_id',
        'type',
        'hours',
        'multiplier',
        'hourly_salary',
        'amount',
        'note',
    ];

    protected $casts = [
        'hours' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'hourly_salary' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
