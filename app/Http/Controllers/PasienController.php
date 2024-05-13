<?php

namespace App\Http\Controllers;

class PasienController extends Controller
{
    public function inquiryStatus()
    {
        $response = [
            'responseCode' => '5047300',
            'responseMessage' => 'Timeout',
        ];
        return response()->json($response, 500);
    }

    public function login()
    {
        $response = [
            'responseCode' => '5047300',
            'responseMessage' => 'Timeout',
        ];
        return response()->json($response, 500);
    }
}
