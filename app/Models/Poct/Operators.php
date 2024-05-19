<?php

namespace App\Models\Poct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operators extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'Operator';
}
