<?php

namespace App\Http\Controllers\Poct;

use App\Http\Controllers\Controller;
use App\Models\Poct\Request\ListPemeriksaanRequest;
use App\Models\Poct\Request\ListPemeriksaanResponse;
use App\Services\Poct\ListPemeriksaanService;
use Illuminate\Http\Request;
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
            $proses = $listPemeriksaanService->fetchPemeriksaan($data, $result, 'list');
            $response = $proses->toArray();
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
            $proses = $listPemeriksaanService->fetchPemeriksaan($data, $result, 'rekap');
            $response = $proses->toArray();
            return response()->json($response, 200);
        } catch (ValidationException $e) {
            $result->setresponseCode("00");
            $result->setresponseMessage($e->getMessage());
            $response = $result->toArray();
            return response()->json($response, 400);
        }

    }
}
