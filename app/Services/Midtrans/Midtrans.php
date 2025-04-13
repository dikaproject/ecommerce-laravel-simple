<?php

namespace App\Services\Midtrans;

use Midtrans\Config;

class Midtrans
{
    public function __construct()
    {
        // Set Midtrans global configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }
}
