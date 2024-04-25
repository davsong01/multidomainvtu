<?php

namespace App\Scopes;

use App\Services\MerchantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;

class MerchantIdScope implements Scope
{
    // public function apply(Builder $builder, Model $model): void
    // {
    //     $builder->where($model->getTable() . '.merchant_id', Request::get('merchant_id'));
    // }

    public function apply(Builder $builder, Model $model)
    {
        $manager = app(MerchantManager::class);
        $builder->where($model->getTable() . 'merchant_id', '=', $manager->getMerchant()->id);
    }

    public function extend(Builder $builder)
    {
        $this->addWithoutTenancy($builder);
    }

    protected function addWithoutTenancy(Builder $builder)
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
