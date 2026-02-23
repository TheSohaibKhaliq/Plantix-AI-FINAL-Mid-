<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StoreFilter extends Model
{
    protected $table = 'store_filters';

    protected $fillable = ['name', 'icon', 'is_active', 'sort_order', 'firebase_doc_id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_store_filters');
    }
}
