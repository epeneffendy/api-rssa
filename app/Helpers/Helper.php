<?php

namespace App\Helpers;


class Helper
{
    public static function isProduction()
    {
        return env('APP_ENV') === 'production';
    }

    public static function isStaging()
    {
        return env('APP_ENV') === 'staging';
    }
}
