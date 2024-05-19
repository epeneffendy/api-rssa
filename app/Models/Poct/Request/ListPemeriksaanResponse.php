<?php

namespace App\Models\Poct\Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPemeriksaanResponse extends \stdClass
{
    private $responseCode = "00";
    private $responseMessage = "Success";
    private $countData = "0";
    private $responseData = array(
        "pasien_id" => "",
        "pasien_norm" => "",
        "pasien_ruangan" => "",
        "pasien_last_up_date" => "",
        "result_id" => "",
        "result_test_name" => "",
        "result_value" => "",
        "result_unit" => "",
        "result_normal_flag" => "",
        "result_speciment_date" => "",
        "operator_first_name" => "",
        "operator_last_name" => "",
    );

    public function __construct($response)
    {
        $has = get_object_vars($this);
        foreach ($has as $name => $oldValue) {
            !array_key_exists($name, $response) ?: $this->$name = $response[$name];
        }
    }

    public function toArray(): array
    {
        $has = get_object_vars($this);
        $response = array();
        foreach ($has as $name => $value) {
            if (gettype($value) === 'object') {
                $response[$name] = $value->toArray();
            } else {
                $response[$name] = $value;
            }
        }
        return $response;
    }

    public function getresponseCode()
    {
        return $this->responseCode;
    }

    public function setresponseCode($responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    public function getresponseMessage()
    {
        return $this->responseMessage;
    }

    public function setresponseMessage($responseMessage): void
    {
        $this->responseMessage = $responseMessage;
    }

    public function getcountData()
    {
        return $this->countData;
    }

    public function setcountData($countData): void
    {
        $this->countData = $countData;
    }

    public function getresponseData(): Collection
    {
        return collect($this->responseData);
    }

    public function setresponseData($responseData): void
    {
        $this->responseData = $responseData;
    }

}
