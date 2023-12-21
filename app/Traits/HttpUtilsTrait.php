<?php

namespace App\Traits;

use App\Traits\EndPointTrait;
use Illuminate\Support\Facades\Http;

trait HttpUtilsTrait
{
    use EndPointTrait;

    public static function makeRequest($url, $method = "POST", $data = [], $headers = [])
    {

        try {

            $headers[] = [
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->$method(env('CARDS_URL') . $url, $data);

            // Accessing the response body as an array
            $responseBody = $response->json();

            return $responseBody;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function checkAcc($MSISDN)
    {

        try {

            $requestRes = $this::makeRequest($this::$CHECK_ACCOUNT, "GET", [
                'phone_number' => $MSISDN,
            ]);

            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function merchantPay($MSISDN,$merchant_code, $txn_amount, $pin)
    {

        try {

            $requestRes = $this::makeRequest($this::$MERCHANT_PAY, "POST", [
                'phone_numer' => $MSISDN,
                "merchant_code" => $merchant_code,
                "txn_amount" => $txn_amount,
                "pin" => $pin,
            ]);

            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function pinReset($MSISDN, $current_pin, $new_pin)
    {
        try {

            $requestRes = $this::makeRequest($this::$PIN_RESET, "POST", [
                'phone_numer' => $MSISDN,
                'current_pin' => $current_pin,
                'new_pin' => $new_pin,
            ]);


            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function blockCard($MSISDN, $pin)
    {
        try {

            $requestRes = $this::makeRequest($this::$BLOCK_CARD, "POST", [
                'phone_numer' => $MSISDN,
                'reason' => "USSD Card Blocking",
                'pin' => $pin,
            ]);

            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function cardAccountRegister($MSISDN, $first_name, $last_name, $card_number, $pin)
    {

        try {

            $requestRes = $this::makeRequest($this::$REGISTER_ACCOUNT, "POST", [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone_number" => $MSISDN,
                "card_number" => $card_number,
                "pin" => $pin,
            ]);

            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


}
