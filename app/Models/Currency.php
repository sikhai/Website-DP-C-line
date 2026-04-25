<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'is_default' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    public static function findByCode($code)
    {
        return self::where('code', strtoupper($code))->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function convertTo($amount, Currency $toCurrency)
    {
        return $amount / $this->exchange_rate * $toCurrency->exchange_rate;
    }

    public function format($amount)
    {
        return ($this->symbol ?? '') . number_format($amount, 2);
    }
}