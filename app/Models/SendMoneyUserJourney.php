<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendMoneyUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number',
        'txn_amount',
        'account_type',
        'card_type',
        'pin',
        'receiver_card_number',
        'institution_name',
        'reference',
    ];

    protected $table = 'send_money_user_journeys';
}
