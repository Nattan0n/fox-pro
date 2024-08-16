<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprcpit extends Model
{
    use HasFactory;
    protected $table = 'aprcpit';
    protected $fillable = ['rcpnum','docnum','rectyp','payamt','vatamt'];


    public function aptrn():BelongsTo{
        return $this->belongsTo(Aptrn::class,'docnum','docnum');
    }
}
