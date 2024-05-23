<?php

namespace App\Models;

use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillerLog extends Model
{
    use HasFactory;
    protected $guarded = [];
}
