<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopUpCardUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number', 'pin', 'step', 'payer_phone_number', 'card_number', 'txn_amount', 'account_type'
    ];

    protected $table = 'top_up_card_user_journeys';
}
