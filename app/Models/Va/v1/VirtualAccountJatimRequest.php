<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 14/05/2024
 * Time: 10:00
 */

namespace App\Models\Va\v1;

class VirtualAccountJatimRequest extends \stdClass
{
    private $VirtualAccount = "";
    private $Nama = "";
    private $Amount = "";
    private $References = "";
    private $Tanggal = "";

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

    public function getVirtualAccount()
    {
        return $this->VirtualAccount;
    }

    public function setVirtualAccount($VirtualAccount): void
    {
        $this->VirtualAccount = $VirtualAccount;
    }

    public function getNama()
    {
        return $this->Nama;
    }

    public function setNama($Nama): void
    {
        $this->Nama = $Nama;
    }

    public function getAmount()
    {
        return $this->Amount;
    }

    public function setAmount($Amount): void
    {
        $this->Amount = $Amount;
    }

    public function getReferences()
    {
        return $this->References;
    }

    public function setReferences($References): void
    {
        $this->References = $References;
    }

    public function getTanggal()
    {
        return $this->Tanggal;
    }

    public function setTanggal($Tanggal): void
    {
        $this->Tanggal = $Tanggal;
    }
}