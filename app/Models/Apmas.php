<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apmas extends Model
{
    use HasFactory;
    protected $table = "apmas";
    protected $fillable = [  'supcod',
        'suptyp',
        'onhold',
        'prenam',
        'supnam',
        'addr01',
        'addr02',
        'addr03',
        'zipcod',
        'telnum',
        'contact',
        'supnam2',
        'paytrm',
        'paycond',
        'dlvby',
        'vatrat',
        'flgvat',
        'disc',
        'balance',
        'chqpay',
        'crline',
        'lasrcv',
        'accnum',
        'remark',
        'taxid',
        'orgnum',
        'taxdes',
        'taxrat',
        'taxtyp',
        'taxcond',
        'creby',
        'credat',
        'userid',
        'chgdat',
        'status',
        'inactdat'];
}
