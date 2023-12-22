<?php

namespace App\Traits;

use App\Models\RegisterUserJourney;
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

    public function cardAccountRegister($MSISDN)
    {

        try {

            $registerJourney = RegisterUserJourney::where('phone_number', '=', $MSISDN)->first();

            if($registerJourney == null){

                return null;
            }

            $requestRes = $this::makeRequest($this::$REGISTER_ACCOUNT, "POST", [
                "first_name" => $registerJourney->first_name,
                "last_name" => $registerJourney->last_name,
                "phone_number" => $registerJourney->MSISDN,
                "card_number" => $registerJourney->card_number,
                "pin" => $registerJourney->pin,
            ]);

            RegisterUserJourney::destroy( $registerJourney->id );

            return $requestRes;

        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


}
