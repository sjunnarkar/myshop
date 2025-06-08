<?php

namespace App\Helpers;

class CurrencyHelper
{
    public static function format($amount)
    {
        return config('app.currency_symbol') . number_format($amount, 2);
    }
} 