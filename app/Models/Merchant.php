<?php

namespace App\Models;

use App\Models\Settings;
use App\Scopes\MerchantIdScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_slug',
        'name',
        'store_name',
        'location',
        'status',
        'subscription_start',
        'subscription_end'
    ];

    public function setting()
    {
        return $this->hasOne(Settings::class)->withoutGlobalScope(new MerchantIdScope());
    }

}
