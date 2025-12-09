<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WarehouseProductTransaction extends Model
{
    const TYPE_IMPORT = 'IMPORT';
    const TYPE_EXPORT = 'EXPORT';

    protected $fillable = [
        'item_id',
        'item_type',
        'unit_id',
        'quantity',
        'extras',
        'type',
        'warehouse_import_id',
        'warehouse_export_id',
        'user_id',
    ];

    protected $casts = [
        'extras' => 'array',
    ];

    // ===== MORPH item (Product | Accessory) =====
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(WarehouseImport::class, 'warehouse_import_id');
    }

    public function export(): BelongsTo
    {
        return $this->belongsTo(WarehouseExport::class, 'warehouse_export_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ========== HÀM TỒN KHO ========== */

    public static function stock($itemId, $itemType, $unitId)
    {
        $in = self::where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->where('unit_id', $unitId)
            ->where('type', self::TYPE_IMPORT)
            ->sum('quantity');

        $out = self::where('item_id', $itemId)
            ->where('item_type', $itemType)
            ->where('unit_id', $unitId)
            ->where('type', self::TYPE_EXPORT)
            ->sum('quantity');

        return $in - $out;
    }
}
