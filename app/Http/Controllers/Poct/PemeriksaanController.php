<?php

namespace App\Http\Controllers\Poct;

use App\Http\Controllers\Controller;
use App\Models\Poct\Request\ListPemeriksaanRequest;
use App\Models\Poct\Request\ListPemeriksaanResponse;
use App\Services\Poct\ListPemeriksaanService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PemeriksaanController extends Controller
{
    public function ListPemeriksaan(Request $request, ListPemeriksaanService $listPemeriksaanService)
    {
        $validator = validator($request->all(), [
            'tgl_order' => ['required', 'date_format:Y-m-d'],
        ], [], [
            'tgl_order' => 'Tgl Order',
        ]);

        $data = new ListPemeriksaanRequest($request->all());
        $result = new ListPemeriksaanResponse($request->all());
        try {
            $validator->validate();
            $timestamp = $request->header('x-timestamp');
            $signarture = $request->header('x-signature');
            $token = $request->header('authorization');

            if (!empty($timestamp) && !empty($signarture) && !empty($token)) {
                if ($token == env('PEC_TOKEN')){
                    $verif_signarture = $this->generateSignature($token, $timestamp);
                    if ($verif_signarture == $signarture) {
                        $proses = $listPemeriksaanService->fetchPemeriksaan($data, $result, 'list');
                        $response = $proses->toArray();
                    } else {
                        $result->setresponseCode("01");
                        $result->setresponseMessage("Unauthorized X-Signature!");
                        $response = $result->toArray();
                    }
                }else{
                    $result->setresponseCode("01");
                    $result->setresponseMessage("Unauthorized Token!");
                    $response = $result->toArray();
                }
            } else {
                $result->setresponseCode("01");
                $result->setresponseMessage("Invalid X-Timestamp or X-Signature or Token!");
                $response = $result->toArray();
            }
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            $result->setresponseCode("00");
            $result->setresponseMessage($e->getMessage());
            $response = $result->toArray();
            return response()->json($response, 400);
        }
    }

    public function RekapPemeriksaan(Request $request, ListPemeriksaanService $listPemeriksaanService)
    {
        $validator = validator($request->all(), [
            'tgl_awal' => ['required', 'date_format:Y-m-d'],
            'tgl_akhir' => ['required', 'date_format:Y-m-d'],
        ], [], [
            'tgl_awal' => 'Tgl Awal',
            'tgl_akhir' => 'Tgl Akhir',
        ]);

        $data = new ListPemeriksaanRequest($request->all());
        $result = new ListPemeriksaanResponse($request->all());

        try {
            $validator->validate();
            $timestamp = $request->header('x-timestamp');
            $signarture = $request->header('x-signature');
            $token = $request->header('authorization');

            if (!empty($timestamp) && !empty($signarture) && !empty($token)) {
                if ($token == env('PEC_TOKEN')){
                    $verif_signarture = $this->generateSignature($token, $timestamp);
                    if ($verif_signarture == $signarture) {
                        $proses = $listPemeriksaanService->fetchPemeriksaan($data, $result, 'rekap');
                        $response = $proses->toArray();
                        return response()->json($response, 200);
                    } else {
                        $result->setresponseCode("01");
                        $result->setresponseMessage("Unauthorized X-Signature!");
                        $response = $result->toArray();
                    }
                }else{
                    $result->setresponseCode("01");
                    $result->setresponseMessage("Unauthorized Token!");
                    $response = $result->toArray();
                }
            } else {
                $result->setresponseCode("01");
                $result->setresponseMessage("Invalid X-Timestamp or X-Signature or Token!");
                $response = $result->toArray();
            }
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            $result->setresponseCode("00");
            $result->setresponseMessage($e->getMessage());
            $response = $result->toArray();
            return response()->json($response, 400);
        }
    }

    public function Signature(Request $request)
    {
        $signature = $this->generateSignature($request->token, $request->timestamp);
        $response = array(
            "signature" => $signature
        );
        return response()->json($response, 200);
    }

    public function Token(Request $request)
    {
        $strToken = Str::random(60);
        $response = array(
            "token" => $strToken
        );
        return response()->json($response, 200);
    }

    public function generateSignature($token, $timestamp)
    {
        $strToString = env('PEC_TOKEN') . '_' . $timestamp;
        $signature = hash('sha256', $strToString);
        return $signature;
    }
}
