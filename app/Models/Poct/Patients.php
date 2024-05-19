<?php

namespace App\Models\Poct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'Patient';

    public function results(){
        return $this->hasMany(Results::class,'_PID', 'ID');
    }
}
