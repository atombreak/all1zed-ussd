<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UtilsTrait
{

    public function generateUniqueString()
    {
        return Str::random(22) . now()->timestamp;
    }

    public function ExistingUserOptions()
    {

        return [
            [
                "id" => 1,
                "name" => "Topup Card",
            ],
            [
                "id" => 2,
                "name" => "Check Balance",
            ],
            [
                "id" => 3,
                "name" => "Send Money",
            ],
            [
                "id" => 4,
                "name" => "Pay Merchant",
            ],
            [
                "id" => 5,
                "name" => "Reset-Pin",
            ],
            [
                "id" => 6,
                "name" => "Block Card",
            ],
            [
                "id" => 7,
                "name" => "Buy Insurance",
            ]
        ];
    }

    public function NewUserOptions()
    {

        return [
            [
                "id" => 1,
                "name" => "Register Card",
            ],
            [
                "id" => 2,
                "name" => "Buy Insurance",
            ],
        ];
    }

    public function options($userExists)
    {
        try {

            return $userExists ? $this->NewUserOptions() :  $this->ExistingUserOptions();

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }
}
