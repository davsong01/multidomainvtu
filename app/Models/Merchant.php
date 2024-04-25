<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function route($name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, array_merge([$this->store_slug], $parameters), $absolute);
    }
}
