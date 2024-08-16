<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Aptrn extends Model
{
    use HasFactory;
    protected $table = "aptrn";
      protected $fillable = [
        'rectyp',
        'docnum',
        'docdata',
        'refnum',
        'vatprd',
        'vatlate',
        'vattyp',
        'postgl',
        'ponum',
        'dntyp',
        'depcod',
        'flgvat',
        'supcod',
        'shipto',
        'youref',
        'paytrm',
        'duedat',
        'bilnum',
        'dlvby',
        'nxtsqy',
        'amount',
        'disc',
        'discamt',
        'aftdisc',
        'advnum',
        'advamt',
        'total',
        'amtrat0',
        'vatrat',
        'vatamt',
        'netamt',
        'netval',
        'payamt',
        'remamt',
        'cmplapp',
        'cmpldat',
        'docstat',
        'cshpay',
        'chqpay',
        'intpay',
        'tax',
        'rcvamt',
        'chqpas',
        'vatdat',
        'srv_vattyp',
        'pvatprorat',
        'pvat_rf',
        'pvat_nrf',
        'userid',
        'chgdat',
        'userprn',
        'prndat',
        'prncnt',
        'prntim',
        'authid',
        'approve',
        'billbe',
        'orgnum',
    ];

    public function aprcpit():HasMany{
      return $this->hasMany(Aprcpit::class,'docnum','docnum');
    }

    public function apmas():BelongsTo{
      return $this->belongsTo(Apmas::class,'supcod','supcod');
    }

    public function bktrn():BelongsTo{
      return $this->belongsTo(Bktrn::class,'supcod','cuscod');
    }
    public function aprcpitByRcpnum():HasOne{
      return $this->hasOne(Aprcpit::class,'rcpnum','docnum');
     } 
}
