<?php

namespace App\Models\Poct\Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPemeriksaanRequest extends \stdClass
{
    private $norm ="";
    private $tgl_order = "";

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

    public function getnorm()
    {
        return $this->norm;
    }

    public function setnorm($norm): void
    {
        $this->norm = $norm;
    }

    public function gettgl_order()
    {
        return $this->tgl_order;
    }

    public function settgl_order($tgl_order): void
    {
        $this->tgl_order = $tgl_order;
    }

}
