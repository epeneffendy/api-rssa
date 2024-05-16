<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentJatimLogs extends Model
{
    use HasFactory;
    protected $table = 'payment_jatim_logs';
    protected $fillable = [
        'type',
        'request',
        'response',
        'response',
        'created_at',
        'updated_at'
    ];

}
