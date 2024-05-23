<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MerchantIdTrait;


class KycData extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }
}
