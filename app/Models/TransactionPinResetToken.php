<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MerchantIdTrait;

class TransactionPinResetToken extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $fillable = ['user_id', 'token', 'expiry','merchant_id'];
}
