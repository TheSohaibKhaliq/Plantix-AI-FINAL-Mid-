<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnReason extends Model
{
    protected $fillable = ['reason', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnRequest::class, 'return_reason_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
