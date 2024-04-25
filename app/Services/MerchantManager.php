<?php

namespace App\Services;

use App\Models\Merchant;

class MerchantManager{
    /*
     * @var null|App\Tenant
     */
    private $merchant;

    public function setMerchant(?Merchant $merchant)
    {
        $this->merchant = $merchant;
        return $this;
    }

    public function getMerchant(): ?Merchant
    {
        return $this->merchant;
    }

    public function loadMerchant($identifier): bool
    {
        $merchant = Merchant::query()->where('store_slug', '=', $identifier)->first();

        if ($merchant) {
            $this->setMerchant($merchant);
            return true;
        }

        return false;
        // Identify the merchant here
    }

    public static function unique($table, $column = 'NULL')
    {
        return (new Rules\Unique($table, $column))->where('merchant_id', $this->getMerchant()->id);
    }

    public static function exists($table, $column = 'NULL')
    {
        return (new Rules\Exists($table, $column))->where('merchant_id', $this->getMerchant()->id);
    }
}