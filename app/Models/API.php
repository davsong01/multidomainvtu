<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Variation;
use App\Models\TransactionLog;
use App\Traits\MerchantIdTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Api extends Model
{

    use HasFactory, MerchantIdTrait;
    protected $guarded = [];
    protected $table = 'apis';

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date("M jS, Y", strtotime($value)),
        );
    }
    
    public function products(){
        return $this->hasMany(Product::class, 'api_id');
    }

    public function variations()
    {
        return $this->hasMany(Variation::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionLog::class, 'api_id');
    }

}
