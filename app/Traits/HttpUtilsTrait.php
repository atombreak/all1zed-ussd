<?php

namespace App\Traits;

use App\Enums\EndPointEnum;
use Illuminate\Support\Facades\Http;

trait HttpUtilsTrait
{

    private EndPointEnum $endPointEnum;

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

            $requestRes = $this::makeRequest(EndPointEnum::CHECK_ACCOUNT, "GET", [
                'phone_numer' => $MSISDN,
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

            $requestRes = $this::makeRequest(EndPointEnum::CHECK_ACCOUNT, "POST", [
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

            $requestRes = $this::makeRequest(EndPointEnum::PIN_RESET, "POST", [
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

            $requestRes = $this::makeRequest(EndPointEnum::BLOCK_CARD, "POST", [
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
}
