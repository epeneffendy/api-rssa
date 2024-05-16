<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 14/05/2024
 * Time: 9:43
 */

namespace App\Services\Va\v1;


use App\Models\PaymentJatimLogs;
use App\Models\PaymentVirtualAccount;
use App\Models\PaymentVirtualAccountDetail;
use App\Models\Va\v1\VirtualAccountJatimRequest;
use App\Models\Va\v1\VirtualAccountJatimResponse;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

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

    public function updatePayment(VirtualAccountJatimRequest $data, VirtualAccountJatimResponse $result)
    {
        DB::beginTransaction();
        try{
            $pembayaran = PaymentVirtualAccount::where('virtual_account', $data->getVirtualAccount())->first();
            if (!$pembayaran) {
                $result->setStatus(array(
                    "IsError" => "True",
                    "ResponseCode" => "01",
                    "ErrorDesc" => "Virtual Account Tidak Ditemukan!"
                ));
            } else {
                if ($pembayaran->flags_lunas == "F") {
                    $result->setStatus(array(
                        "IsError" => "True",
                        "ResponseCode" => "01",
                        "ErrorDesc" => "Tagihan anda telah lunas!"
                    ));
                } else {
                    if ($pembayaran->endpoint == "full") {
                        $pembayaran->bayar = $data->getAmount();
                        $pembayaran->flags_lunas = "F";
                        $pembayaran->save();
                    } else {
                        if ($data->getAmount() > $pembayaran->totalamount) {
                            $result->setStatus(array(
                                "IsError" => "True",
                                "ResponseCode" => "01",
                                "ErrorDesc" => "Nominal bayar melebihi jumlah tagihan!"
                            ));
                        } else {
                            $sisa = $pembayaran->totalamount - $pembayaran->bayar;
                            if ($data->getAmount() > $sisa) {
                                $result->setStatus(array(
                                    "IsError" => "True",
                                    "ResponseCode" => "01",
                                    "ErrorDesc" => "Nominal bayar melebihi sisa jumlah tagihan!"
                                ));
                            } else {

                                $pembayaran->bayar = $pembayaran->bayar + $data->getAmount();
                                $pembayaran->flags_lunas = ($pembayaran->bayar == $pembayaran->totalamount) ? "F" : "O";
                                $pembayaran->save();

                                if (empty($pembayaran->bayar)){
                                    $detail_sisa = $pembayaran->totalamount - $data->getAmount();
                                }else{
                                    $detail_sisa = $pembayaran->totalamount - $pembayaran->bayar;
                                }

                                //insert history pembayaran partial
                                $detail = new PaymentVirtualAccountDetail();
                                $detail->payment_virtualaccount_id = $pembayaran->id;
                                $detail->nomr = $pembayaran->nomr;
                                $detail->idxdaftar = $pembayaran->idxdaftar;
                                $detail->bayar = $data->getAmount();
                                $detail->sisabayar = $detail_sisa;
                                $detail->save();
                            }
                        }
                    }
                }

            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
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