<?php

namespace App\Models;

use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $guarded = [];
}
