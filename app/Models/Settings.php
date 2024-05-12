<?php

namespace App\Models;

use App\Models\Merchant;
use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settings extends Model
{
    use HasFactory, MerchantIdTrait;
    protected $guarded = [];

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }
}
