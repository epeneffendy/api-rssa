<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 13/05/2024
 * Time: 10:56
 */

namespace App\Models\Qris\v1;

class QrisJatimPaymentResponse extends \stdClass
{
    private $responsCode = "00";
    private $responsDesc = "Success";
    private $billNumber = "";
    private $purposetrx = "";
    private $storelabel = "";
    private $customerlabel = "";
    private $terminalUser = "";
    private $amount = "";
    private $core_reference = "";
    private $customerPan = "";
    private $merchantPan = "";
    private $pjsp = "";
    private $invoice_number = "";
    private $transactionDate = "";

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

    public function getresponsCode()
    {
        return $this->responsCode;
    }

    public function setresponsCode($responsCode): void
    {
        $this->responsCode = $responsCode;
    }

    public function getresponsDesc()
    {
        return $this->responsDesc;
    }

    public function setresponsDesc($responsDesc): void
    {
        $this->responsDesc = $responsDesc;
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
        $this->customerlabel= $customerlabel;
    }

    public function getterminalUser()
    {
        return $this->terminalUser;
    }

    public function setterminalUser($terminalUser): void
    {
        $this->terminalUser = $terminalUser;
    }

    public function getamount()
    {
        return $this->amount;
    }

    public function setamount($amount): void
    {
        $this->amount= $amount;
    }

    public function gettransactionDate()
    {
        return $this->transactionDate;
    }

    public function settransactionDate($transactionDate): void
    {
        $this->transactionDate = $transactionDate;
    }

    public function getcore_reference ()
    {
        return $this->core_reference ;
    }

    public function setcore_reference ($core_reference ): void
    {
        $this->core_reference  = $core_reference ;
    }

    public function getccustomerPan ()
    {
        return $this->customerPan ;
    }

    public function setcustomerPan  ($customerPan ): void
    {
        $this->customerPan = $customerPan  ;
    }

    public function getpjsp()
    {
        return $this->pjsp;
    }

    public function setpjsp($pjsp): void
    {
        $this->pjsp = $pjsp;
    }

    public function getinvoice_number()
    {
        return $this->invoice_number;
    }

    public function setinvoice_number($invoice_number): void
    {
        $this->invoice_number = $invoice_number;
    }


}