<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPaymentAttachment extends Model
{
    protected $fillable = [
        'payroll_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
