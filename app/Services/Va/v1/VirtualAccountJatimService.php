<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 14/05/2024
 * Time: 9:43
 */

namespace App\Services\Va\v1;


use GuzzleHttp\Client;

class VirtualAccountJatimService
{
    protected $settings;
    protected $defaultHeaders = [];

    public function __construct()
    {
        $this->settings = array(
            'apiUrl' => env('BANK_JATIM_URL', 'https://jatimva.bankjatim.co.id/'),
            'merchant' => env('BANK_JATIM_MERCHANT', '9360011400001347721'),
            'hashcode' => env('BANK_JATIM_HASCODE', 'Y1MACZ4B5R'),
            'terminalUser' => env('BANK_JATIM_TERMINAL_USER', 'ID2024310949969'),
            'username' => env('BANK_JATIM_USERNAME', 'ID2024310949969'),
            'password' => env('BANK_JATIM_PASSWORD', 'ID2024310949969'),
        );
        $this->defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Origin' => request()->getHost(),
        ];
        $configs = [
            'base_uri' => $this->settings['apiUrl'],
            'headers' => $this->defaultHeaders,
        ];

        $this->client = new Client($configs);
    }
}