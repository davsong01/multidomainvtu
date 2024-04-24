<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;

class MerchantIdScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Your logic to apply the scope
        $builder->where('merchant_id', Request::get('merchant_id'));
    }
}
