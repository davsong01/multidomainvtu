<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Merchant;
use App\Scopes\MerchantIdScope;
use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReservedAccountNumber extends Model
{
    use HasFactory, MerchantIdTrait;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cron_customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withoutGlobalScope(new MerchantIdScope());
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionLog::class, 'account_number', 'id');
    }

    public function payment_gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'id');
    }

    public function gateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'paymentgateway_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
