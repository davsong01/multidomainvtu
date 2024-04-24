<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MerchantIdTrait;


class PaymentGateway extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $guarded = [];
}
