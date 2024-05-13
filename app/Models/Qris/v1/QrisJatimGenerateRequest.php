<?php

/**
 * Created by PhpStorm.
 * User: ITKOM-EFFENDY
 * Date: 01/04/2024
 * Time: 14:48
 */

namespace App\Models\Qris\v1;

class QrisJatimGenerateRequest extends \stdClass
{
    private $merchantPan = "";
    private $hashcodeKey = "";
    private $billNumber = "";
    private $purposetrx = "";
    private $storelabel = "";
    private $customerlabel = "";
    private $terminalUser = "";
    private $amount = "";
    private $expiredDate = "";

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

    public function getmerchantPan()
    {
        return $this->merchantPan;
    }

    public function setmerchantPan($merchantPan): void
    {
        $this->merchantPan = $merchantPan;
    }

    public function gethashcodeKey()
    {
        return $this->hashcodeKey;
    }

    public function sethashcodeKey($hashcodeKey): void
    {
        $this->hashcodeKey = $hashcodeKey;
    }

    public function getbillNumber()
    {
        return $this->billNumber;
    }

    public function setbillNumber($billNumber): void
    {
        $this->billNumber = $billNumber;
    }

    public function getpurposetrx()
    {
        return $this->purposetrx;
    }

    public function setpurposetrx($purposetrx): void
    {
        $this->purposetrx = $purposetrx;
    }

    public function getstorelabel()
    {
        return $this->storelabel;
    }

    public function setstorelabel($storelabel): void
    {
        $this->storelabel = $storelabel;
    }

    public function getcustomerlabel()
    {
        return $this->customerlabel;
    }

    public function setcustomerlabel($customerlabel): void
    {
        $this->customerlabel = $customerlabel;
    }

    public function getterminalUser()
    {
        return $this->terminalUser;
    }

    public function setterminalUser($terminalUser): void
    {
        $this->terminalUser = $terminalUser;
    }

    public function getexpiredDate()
    {
        return $this->expiredDate;
    }

    public function setexpiredDate($expiredDate): void
    {
        $this->expiredDate = $expiredDate;
    }

    public function getamount()
    {
        return $this->amount;
    }

    public function setamount($amount): void
    {
        $this->amount = $amount;
    }
}
