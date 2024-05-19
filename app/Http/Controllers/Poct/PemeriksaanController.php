<?php

namespace App\Http\Controllers\Poct;

use App\Http\Controllers\Controller;
use App\Models\Poct\Request\ListPemeriksaanRequest;
use App\Models\Poct\Request\ListPemeriksaanResponse;
use App\Services\Poct\ListPemeriksaanService;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    public function ListPemeriksaan(Request $request, ListPemeriksaanService $listPemeriksaanService)
    {
        $validator = validator($request->all(), [
            'norm' => ['required', 'digits_between:6,8'],
            'tgl_order' => ['required', 'date_format:Y-m-d'],
        ], [], [
            'norm' => 'Norm',
            'tgl_order' => 'Tgl Order',
        ]);

        try {
            $validator->validate();
            $data = new ListPemeriksaanRequest($request->all());
            $result = new ListPemeriksaanResponse($request->all());

            $proses = $listPemeriksaanService->fetchPemeriksaan($data, $result);
            $response = $proses->toArray();
            return response()->json($response, 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
