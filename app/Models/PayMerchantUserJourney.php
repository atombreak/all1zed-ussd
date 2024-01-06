<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayMerchantUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number', 'merchant_code', 'txn_amount', 'pin', 'step'
    ];

    protected $table = 'pay_merchant_user_journeys';

}
