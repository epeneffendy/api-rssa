<?php

namespace App\Services\Qris\v1;

use App\Models\PaymentBank;
use App\Models\PaymentJatimLogs;
use App\Models\Qris\v1\QrisJatimPaymentRequest;
use App\Models\Qris\v1\QrisJatimPaymentResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class QrisJatimService
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

    public function generateApiQris($data)
    {
        $aa = "";
        try {
            $request = $this->client->post('/MC/Qris/Dynamic', [
                'json' => $aa
            ]);
            $response = json_decode($request->getBody()->getContents());
        } catch (RequestException $e) {
            dd($e);
            $response = json_decode($e->getResponse()->getBody()->getContents());

//            $this->log('va/status', $data, $e->getResponse()->getBody()->getContents());
        }
        return $response;

    }

    public function checkStatusQrisPayment($data)
    {
        try {
            $request = $this->client->post('/MC/PaymentQr', [
                'json' => $data
            ]);
            $response = json_decode($request->getBody()->getContents());
        } catch (RequestException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents());
        }

        return $response;
    }

    public function updateQris(QrisJatimPaymentRequest $data, QrisJatimPaymentResponse $result)
    {
        $pembayaran = PaymentBank::where('invoice_number', $data->getinvoice_number())->first();

            if (!$pembayaran){
                $result->setresponsCode("01");
                $result->setresponsDesc("Data Pembayaran Qris tidak ditemukan!");
            }else{
                if ($pembayaran->payment_status == 1) {
                    $pembayaran->payment_status = 2;
                    $pembayaran->save();
                }else{
                    $result->setresponsCode("01");
                    $result->setresponsDesc("Data Pembayaran Qris telah terkonfirmasi!");
                }
            }

        return $result;
    }

    public function log($type, $request, $response)
    {
        $log = PaymentJatimLogs::create([
            'type' => $type,
            'request' => json_encode($request),
            'response' => json_encode($response),
        ]);
        return $log;
    }
}
