<?php

/**
 * Created by PhpStorm.
 * User: ITKOM-EFFENDY
 * Date: 01/04/2024
 * Time: 14:48
 */

namespace App\Models\Qris\v1;

class QrisJatimGenerateResponse extends \stdClass
{
    private $responseCode = '00';
    private $totalAmount = '';
    private $qrValue = '';
    private $amount = '';
    private $expiredDate = '';
    private $nmid = '';
    private $billNumber = '';
    private $merchantPan = '';
    private $invoiceNumber = '';
    private $status = '';
    private $merchantName = '';

    public function __construct($response = null)
    {
        if ($response !== null) {
            $has = get_object_vars($this);
            foreach ($has as $name => $oldValue) {
                !array_key_exists($name, $response) ?: $this->{'set' . $name}($response[$name]);
            }
        }
    }

    public function toArray()
    {
        $has = get_object_vars($this);
        $response = array();
        foreach ($has as $name => $value) {
            $response[$name] = $value;
        }
        return $response;
    }

    public function getresponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param mixed $responseCode
     */
    public function setresponseCode($responseCode): void
    {
        $this->responseCode = $responseCode;
    }

    public function gettotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * @param mixed $totalAmount
     */
    public function settotalAmount($totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getqrValue()
    {
        return $this->qrValue;
    }

    /**
     * @param mixed $qrValue
     */
    public function setqrValue($qrValue): void
    {
        $this->qrValue = $qrValue;
    }


    public function getamount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setamount($amount): void
    {
        $this->amount = $amount;
    }

    public function getexpiredDate()
    {
        return $this->expiredDate;
    }

    /**
     * @param mixed $expiredDate
     */
    public function setexpiredDate($expiredDate): void
    {
        $this->expiredDate = $expiredDate;
    }

    public function getnmid()
    {
        return $this->nmid;
    }

    /**
     * @param mixed $nmid
     */
    public function setnmid($nmid): void
    {
        $this->nmid = $nmid;
    }

    public function getbillNumber()
    {
        return $this->billNumber;
    }

    /**
     * @param mixed $billNumber
     */
    public function setbillNumber($billNumber): void
    {
        $this->billNumber = $billNumber;
    }

    public function getmerchantPan()
    {
        return $this->merchantPan;
    }

    /**
     * @param mixed $merchantPan
     */
    public function setmerchantPan($merchantPan): void
    {
        $this->merchantPan = $merchantPan;
    }

    public function getinvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param mixed $invoiceNumber
     */
    public function setinvoiceNumber($invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getstatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setstatus($status): void
    {
        $this->status = $status;
    }

    public function getmerchantName()
    {
        return $this->merchantName;
    }

    /**
     * @param mixed $merchantName
     */
    public function setmerchantName($merchantName): void
    {
        $this->merchantName = $merchantName;
    }
}
