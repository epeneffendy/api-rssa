<?php

namespace App\Services;

class RouteService
{
    public function isProduction()
    {
        return env('APP_ENV') === 'production';
    }

    public function isStaging()
    {
        return env('APP_ENV') === 'staging';
    }

    public function getPaymentsSubdomain()
    {
        $domain = request()->getHost();
        return $domain;
    }
}
