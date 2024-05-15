<?php

namespace App\Models;

use App\Models\TransactionLog;
use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory, MerchantIdTrait;
    protected $fillable = ['customer_id','amount','type','transaction_id','reason','merchant_id'];

    public function transaction_log(){
        return $this->hasOne(TransactionLog::class, 'transaction_id','transaction_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
