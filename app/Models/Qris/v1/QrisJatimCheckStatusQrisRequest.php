<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 11/05/2024
 * Time: 13:07
 */

namespace App\Models\Qris\v1;

class QrisJatimCheckStatusQrisRequest extends \stdClass
{
    private $username = "";
    private $password = "";
    private $invoice_number = "";

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

    public function getusername()
    {
        return $this->username;
    }

    public function setusername($username): void
    {
        $this->username = $username;
    }

    public function getpassword()
    {
        return $this->password;
    }

    public function setpassword($password): void
    {
        $this->password = $password;
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