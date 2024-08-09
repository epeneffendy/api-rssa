<?php

/**
 * Created by PhpStorm.
 * User: USER
 * Date: 14/05/2024
 * Time: 9:43
 */

namespace App\Services\Va\v1;

use App\Models\BayarRajal;
use App\Models\BayarRanap;
use App\Models\CloseKasir;
use App\Models\CloseKasirDetail;
use App\Models\MaxNomerBayar;
use App\Models\PaymentJatimLogs;
use App\Models\PaymentVirtualAccount;
use App\Models\PaymentVirtualAccountDetail;
use App\Models\PelunasanPiutang;
use App\Models\Piutang;
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
        try {
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
                        if ($pembayaran->type == 'closing') {
                            $closing = $this->closingKasir($pembayaran, $data);
                        }

                        if ($pembayaran->type == 'piutang') {
                            $piutang = Piutang::where('nobilling', $pembayaran->billnumber)->get();

                            if (count($piutang) > 0) {
                                $date_now = date('Y-m-d');
                                foreach ($piutang as $item) {
                                    $no_kwitansi = $this->lastNoBayar($item->nip, $date_now, $item->shift);

                                    $pelunasan['id_piutang'] = $item->id_piutang;
                                    $pelunasan['tgl_pelunasan'] = $date_now;
                                    $pelunasan['jumlah_piutang'] = $item->jumlah_bayar;
                                    $pelunasan['jumlah_bayar'] = $item->jumlah_bayar;
                                    $pelunasan['no_kwitansi'] = $no_kwitansi;
                                    $pelunasan['tgl_entri'] = $date_now;
                                    $pelunasan['petugas_entri'] = $item->nip;
                                    $pelunasan['shift'] = $item->shift;
                                    $save = PelunasanPiutang::insert($pelunasan);
                                    //update status lunas di piutang
                                    $lunas = Piutang::where('id_piutang', $item->id_piutang)->update(['st_piutang' => 'LUNAS']);

                                    $data_piutang = Piutang::where('id_piutang', $item->id_piutang)->first();
                                    if (!empty($data_piutang)) {
                                        $payload['lunas'] = 1;
                                        $payload['status'] = 'LUNAS';
                                        $payload['jmbayar'] = $item->jumlah_bayar;
                                        $payload['tglbayar'] = $date_now;
                                        $payload['jambayar'] = date('H:i:s');
                                        $payload['nobayar'] = $no_kwitansi;
                                        $payload['nip'] = $item->nip;
                                        $payload['shift'] = $item->shift;
                                        if ($data_piutang['st_billing'] == 'IRNA') {
                                            $update_bayar = BayarRanap::where('idxbill', $data_piutang->idxbill)->update($payload);
                                        } else if ($data_piutang['st_billing'] == 'IRNA') {
                                            $update_bayar = BayarRanap::where('idxbill', $data_piutang->idxbill)->update($payload);
                                        } else {
                                            $update_bayar = BayarRanap::where('idxbill', $data_piutang->idxbill)->update($payload);
                                        }

                                        $arr_max['nomor'] = $no_kwitansi;
                                        $arr_max['type'] = 'kuitansi';
                                        $arr_max['petugas'] = $item->nip;
                                        $arr_max['tanggal'] = $date_now;
                                        $arr_max['shift'] = $item->shift;
                                        $update_max = MaxNomerBayar::where(['type' => 'kuitansi', 'petugas' => $item->nip, 'tanggal' => $date_now, 'shift' => $item->shift])->update($arr_max);
                                    }
                                }
                            }
                        }
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

                                if (empty($pembayaran->bayar)) {
                                    $detail_sisa = $pembayaran->totalamount - $data->getAmount();
                                } else {
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

                                if ($pembayaran->type == 'closing') {
                                    $closing = $this->closingKasir($pembayaran, $data);
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
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

    function lastNoBayar($petugas, $tanggal, $shift)
    {
        $kwitansi = MaxNomerBayar::where(['type' => 'kwitansi', 'petugas' => $petugas, 'tanggal' => $tanggal, 'shift' => $shift])->first();
        $pre_no = $lastNoBayar = $xLastNoBayar = null;
        $pre_no = date('Y');
        if (isset($kwitansi)) {
            $lastNoBayar = ((int)$kwitansi->nomor) + 1;
            $xLastNoBayar = str_pad($lastNoBayar, 7, 0, STR_PAD_LEFT);
            return $xLastNoBayar;
        } else {
            $new_nobayar = '0000001';
            $payload['nomor'] = $new_nobayar;
            $payload['type'] = 'kuitansi';
            $payload['petugas'] = $petugas;
            $payload['tanggal'] = $tanggal;
            $payload['shift'] = $shift;
            $nomor = MaxNomerBayar::insert($payload);
            return $new_nobayar;
        }
    }

    function closingKasir($pembayaran, $data)
    {

        //cari data closing
        $closing = CloseKasir::where('virtual_account', $data->getVirtualAccount())->first();
        if (isset($closing)) {
            $lunas = CloseKasir::where('virtual_account', $data->getVirtualAccount())->update(['st_bayar_payment' => 'bayar']);
            $lunas_detail = CloseKasirDetail::where('close_kasir_id', $closing->id)->update(['st_bayar_payment' => 'bayar']);

            if ($closing->billing == 'RAWAT JALAN') {
                $lunas_billing = BayarRajal::where('virtual_account', $data->getVirtualAccount())->update(['st_bayar_payment' => 'bayar']);
            } else {
                $lunas_billing = BayarRanap::where('virtual_account', $data->getVirtualAccount())->update(['st_bayar_payment' => 'bayar']);
            }
        }
    }
}
