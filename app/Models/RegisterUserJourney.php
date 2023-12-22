<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'first_name', 'last_name', 'phone_number', 'card_number', 'pin', 'step'
    ];

    protected $table = 'register_user_journeys';
}
