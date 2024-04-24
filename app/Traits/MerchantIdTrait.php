<?php
    namespace App\Traits;

    use App\Scopes\MerchantIdScope;
    
    trait MerchantIdTrait{
        protected static function boot(){
            parent::boot();

            static::addGlobalScope(new MerchantIdScope());
        }
    }
?>