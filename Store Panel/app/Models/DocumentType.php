<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'name', 'type', 'is_required', 'is_enabled', 'firebase_doc_id',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_enabled'  => 'boolean',
    ];

    public function verifications(): HasMany
    {
        return $this->hasMany(DocumentVerification::class);
    }
}
