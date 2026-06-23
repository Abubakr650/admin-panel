<?php
// Idempotency is used to prevent duplicate requests
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class Idempotency
{
    public static function check($key)
    {
        if (Cache::has($key)) {
            return true;
        }

        Cache::put($key, true, now()->addMinutes(10));

        return false;
    }
}