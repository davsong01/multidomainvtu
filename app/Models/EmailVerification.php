<?php

namespace App\Models;

use App\Models\User;
use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailVerification extends Model
{
    use HasFactory, MerchantIdTrait;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
