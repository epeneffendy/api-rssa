<?php

namespace App\Services\Poct;

use App\Models\Poct\Patients;
use App\Models\Poct\Request\ListPemeriksaanRequest;
use App\Models\Poct\Request\ListPemeriksaanResponse;
use Illuminate\Support\Facades\DB;

class ListPemeriksaanService
{
    public function fetchPemeriksaan(ListPemeriksaanRequest $data, ListPemeriksaanResponse $result)
    {
        $list_pemeriksaan = DB::connection('sqlsrv')->table('Patient')
            ->select('Patient.ID as pasien_id', 'Patient.Lab_PatientID as pasien_norm', 'Patient.Location as pasien_ruangan', 'Patient.lastUpdDatetime as pasien_last_up_date', 'Result.ID as result_id',
                'Result.UnivTestName as result_test_name', 'Result.RValue as result_value', 'Result.Unit as result_unit', 'Result.ANormalFlag as result_normal_flag',
                'Result.TestEndDate as result_speciment_date', 'Operator.FirstName as operator_first_name', 'Operator.LastName as operator_last_name')
            ->leftJoin('Result', 'Result._PID', '=', 'Patient.ID')
            ->leftJoin('Operator', 'Operator.OperatorID', '=', 'Result.OperatiorID')
            ->whereNotNull('Result.RValue')
            ->whereRaw('LEN(Patient.Lab_PatientID) >= 10')
            ->where(DB::raw("(convert(date,Result.TestEndDate))"), "=", $data->gettgl_order())
            ->orderBy('Result.TestEndDate', 'DESC')
            ->orderBy('Patient.Lab_PatientID', 'DESC')
            ->limit(10)
            ->get();

        if (count($list_pemeriksaan) > 0) {
            $result->setcountData(count($list_pemeriksaan));
            $aa = [];

            foreach ($list_pemeriksaan as $ind => $item) {
                dd($item);
                $aa = array(
                    "pasien_id" => $item->pasien_id,
                    "pasien_norm" => $item->pasien_norm,
                    "pasien_ruangan" => $item->pasien_ruangan,
                    "pasien_last_up_date" => $item->pasien_last_up_date,
                    "result_id" => $item->result_id,
                    "result_test_name" => $item->result_test_name,
                    "result_value" => $item->result_value,
                    "result_unit" => $item->result_unit,
                    "result_normal_flag" => $item->result_normal_flag,
                    "result_speciment_date" => $item->result_speciment_date,
                    "operator_first_name" => $item->operator_first_name,
                    "operator_last_name" => $item->operator_last_name,
                );
//                $result->setresponseData(array(
//
//                ));
            }
//            $result->setresponseData($aa);
        }else{
            $result->setresponseCode("01");
            $result->setresponseMessage("Data Pemeriksaan Tidak Ditemukan!");
        }
dd($aa);
        dd($result);
    }
}