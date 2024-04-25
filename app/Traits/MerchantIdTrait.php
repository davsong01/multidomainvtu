<?php

    namespace App\Traits;
    use App\Services\MerchantManager;

    use App\Scopes\MerchantIdScope;
    
    trait MerchantIdTrait{
        public static function bootOwnedByTenant() {
            static::addGlobalScope(new TenantOwnedScope);
        
            static::creating(function ($model) {
                if (! $model->tenant_id && ! $model->relationLoaded('merchant')) {
                    $model->setRelation('merchant', app(MerchantManager::class)->getMerchant());
                }
        
                return $model;
            });
        }
        
        public function merchant(){
            $this->belongsTo(Merchant::class, 'merchant_id');
        }
    }
?>