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
                "id" => 0,
                "name" => "Cancel",
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

    public  function isValidPersonName($name)
    {
        // Regular expression for validating a name
        // This regex allows alphabetic characters, spaces, hyphens, and apostrophes
        $regex = "/^[a-zA-Z'\s-]+$/";

        return preg_match($regex, $name);
    }

    public function formatOptionsResponseMsg($options, $header_msg)
    {
        $menu_options = [];
        foreach ($options as $option) {
            $menu_options[] = $option['id'] . '.' . ' ' . $option['name'];
        }

        $response_msg = $header_msg . "\n";

        foreach ($menu_options as $key => $value) {
            $response_msg .= "{$value} \n";
        }

        return $response_msg;
    }

    public function formatResponseMsg($header_msg)
    {

        $response_msg = $header_msg . "\n";

        return $response_msg;
    }

}
