<?php

namespace App\Models\Poct;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Results extends Model
{
    use HasFactory;
    protected $connection = 'sqlsrv';
    protected $table = 'Result';

    public function operators(){
        return $this->hasMany(Operators::class,'OperatorID', 'OperatorID');
    }
}
