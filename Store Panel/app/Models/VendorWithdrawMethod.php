<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class VendorWithdrawMethod extends Model
{
    protected $table = 'vendor_withdraw_methods';

    protected $fillable = ['vendor_id', 'method', 'credentials', 'is_active'];

    protected $hidden   = ['credentials'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Store credentials encrypted */
    public function setCredentialsAttribute(array $value): void
    {
        $this->attributes['credentials'] = Crypt::encryptString(json_encode($value));
    }

    /** Decrypt credentials on read */
    public function getCredentialsAttribute(string $value): array
    {
        return json_decode(Crypt::decryptString($value), true);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
