<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollBonus extends Model
{
    protected $fillable = [
        'payroll_id',
        'name',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
