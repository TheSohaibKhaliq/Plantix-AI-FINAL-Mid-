<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * InventoryLog
 *
 * Immutable audit trail for every stock mutation.
 * Never update or delete rows — only create.
 */
class InventoryLog extends Model
{
    public const UPDATED_AT = null; // single timestamp only

    // Change types
    public const TYPE_SALE       = 'sale';
    public const TYPE_RESTOCK    = 'restock';
    public const TYPE_CANCEL     = 'cancel';
    public const TYPE_RETURN     = 'return';
    public const TYPE_MANUAL     = 'manual';
    public const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'product_id',
        'vendor_id',
        'order_id',
        'return_id',
        'initiated_by',   // user_id of whoever triggered the change
        'type',
        'quantity_before',
        'quantity_change', // negative = reduction, positive = addition
        'quantity_after',
        'notes',
    ];

    protected $casts = [
        'quantity_before' => 'integer',
        'quantity_change' => 'integer',
        'quantity_after'  => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
