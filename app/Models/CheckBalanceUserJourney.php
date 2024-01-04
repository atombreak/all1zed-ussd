<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckBalanceUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number', 'pin', 'step'
    ];

    protected $table = 'check_balance_user_journeys';
}
