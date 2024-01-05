<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPinUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number', 'new_pin', 'step', 'current_pin'
    ];

    protected $table = 'reset_pin_user_journeys';

}
