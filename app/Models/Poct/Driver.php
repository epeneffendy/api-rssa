<?php

namespace App\Models\Poct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'Driver';
}
