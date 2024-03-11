<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class InsuranceUserJourney extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'phone_number', 'insurance_type', 'step'
    ];

    protected $table = 'insurance_user_journeys';
}
