<?php

namespace App\Http\Controllers\Poct;

use App\Http\Controllers\Controller;
use App\Models\Poct\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function GetDriver()
    {
        $aa= Driver::all();
        dd($aa);
    }
}
