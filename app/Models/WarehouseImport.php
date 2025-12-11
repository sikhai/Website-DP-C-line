<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseImport extends Model
{
    protected $fillable = [
        'import_code',
        'user_id',
        'project_id',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(WarehouseProductTransaction::class, 'warehouse_import_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getTotalQuantityAttribute()
    {
        return $this->transactions
            ->where('type', WarehouseProductTransaction::TYPE_IMPORT)
            ->sum('quantity');
    }
}
