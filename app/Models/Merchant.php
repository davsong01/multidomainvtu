<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\MerchantIdTrait;


class Merchant extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $fillable = [
        'store_slug',
        'name',
        'store_name',
        'location',
        'status',
        'subscription_start',
        'subscription_end'
    ];
}
