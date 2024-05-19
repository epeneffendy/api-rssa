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
            ->get();

        if (count($list_pemeriksaan) > 0) {
            $result->setcountData(count($list_pemeriksaan));
            $data = [];
            foreach ($list_pemeriksaan as $ind => $item) {
                $data[$ind]['pasien_id'] = $item->pasien_id;
                $data[$ind]['pasien_norm'] = $item->pasien_norm;
                $data[$ind]['pasien_ruangan'] = $item->pasien_ruangan;
                $data[$ind]['pasien_last_up_date'] = $item->pasien_last_up_date;
                $data[$ind]['result_id'] = $item->result_id;
                $data[$ind]['result_test_name'] = $item->result_test_name;
                $data[$ind]['result_value'] = $item->result_value;
                $data[$ind]['result_unit'] = $item->result_unit;
                $data[$ind]['result_normal_flag'] = $item->result_normal_flag;
                $data[$ind]['result_speciment_date'] = $item->result_speciment_date;
                $data[$ind]['operator_first_name'] = $item->operator_first_name;
                $data[$ind]['operator_last_name'] = $item->operator_last_name;
            }
            $result->setresponseData($data);
        } else {
            $result->setresponseCode("01");
            $result->setresponseMessage("Data Pemeriksaan Tidak Ditemukan!");
        }
        return $result;
    }
}