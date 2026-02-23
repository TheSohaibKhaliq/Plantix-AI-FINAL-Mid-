<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    protected $primaryKey = 'id';

    /** Get a setting value with optional default; result is cached indefinitely */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return $default;

            return match ($setting->type) {
                'json'    => json_decode($setting->value, true),
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                default   => $setting->value,
            };
        });
    }

    /** Set a setting value and bust its cache */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $stored = is_array($value) ? json_encode($value) : (string) $value;
        $type   = is_array($value) ? 'json' : $type;

        static::updateOrCreate(['key' => $key], ['value' => $stored, 'type' => $type]);
        Cache::forget("setting:{$key}");
    }
}
