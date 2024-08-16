<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bktrn extends Model
{
    use HasFactory;

    protected $table = "bktrn";

    protected $fillable = [
        'bktrntyp',
        'trndat',
        'chqnum',
        'chqdat',
        'bnkcod',
        'branch',
        'cuscod',
        'name',
        'depcod',
        'postgl',
        'getdat',
        'payindat',
        'amount',
        'charge',
        'vatamt',
        'netamt',
        'remamt',
        'remcut',
        'cmplapp',
        'chqstat',
        'bnkacc',
        'jnltrntyp',
        'remark',
        'refdoc',
        'refnum',
        'vatdat',
        'vatprd',
        'vatlate',
        'vattyp',
        'voucher',
        'userid',
        'chgdat',
        'authid',
        'approve',
        'taxid',
        'orgnum',
    ];
    public function aprcpit():HasMany{
        return $this->hasMany(Aprcpit::class,'rcpnum','voucher');
    }

    public function apmas():BelongsTo
    {
        return $this->belongsTo(Apmas::class,'cuscod','supcod');
    }
    public function aptrn():HasMany{
        return $this->hasMany(Aptrn::class,'supcod','cuscod');
    }
}
