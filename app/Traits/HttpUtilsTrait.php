<?php

namespace App\Traits;

use App\Models\BlockCardUserJourney;
use App\Models\CheckBalanceUserJourney;
use App\Models\PayMerchantUserJourney;
use App\Models\RegisterUserJourney;
use App\Models\ResetPinUserJourney;
use App\Models\SendMoneyUserJourney;
use App\Models\TopUpCardUserJourney;
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

            //dd($response);
            // Accessing the response body as an array
            $responseJson = $response->json();

            // Get the status code
            $statusCode = $response->status();

            //dd($statusCode);

            return [
                'responseJson' => $responseJson,
                'statusCode' => $statusCode
            ];
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

            //dd();

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function checkBalance($MSISDN, $SUBSCRIBER_INPUT)
    {

        try {

            $checkBalanceJourney = CheckBalanceUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($checkBalanceJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$CHECK_BALANCE, "GET", [
                'phone_number' => $MSISDN,
                'pin' => $SUBSCRIBER_INPUT
            ]);

            CheckBalanceUserJourney::destroy($checkBalanceJourney->id);

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function merchantPay($MSISDN)
    {

        try {

            $payMerchantJourney = PayMerchantUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($payMerchantJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$MERCHANT_PAY, "POST", [
                'phone_number' => $MSISDN,
                "merchant_code" => $payMerchantJourney->merchant_code,
                "txn_amount" => $payMerchantJourney->txn_amount,
                "pin" => $payMerchantJourney->pin,
            ]);

            PayMerchantUserJourney::destroy($payMerchantJourney->id);

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function pinReset($MSISDN)
    {
        try {

            $resetPinUserJourney = ResetPinUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($resetPinUserJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$PIN_RESET, "POST", [
                'phone_number' => $MSISDN,
                'current_pin' => $resetPinUserJourney->current_pin,
                'new_pin' => $resetPinUserJourney->new_pin,
            ]);

            //dd($requestRes);

            ResetPinUserJourney::destroy($resetPinUserJourney->id);


            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function blockCard($MSISDN)
    {
        try {

            $blockCardUserJourney = BlockCardUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($blockCardUserJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$BLOCK_CARD, "POST", [
                'phone_number' => $MSISDN,
                'reason' => $blockCardUserJourney->reason,
                'pin' => $blockCardUserJourney->pin,
            ]);

            BlockCardUserJourney::destroy($blockCardUserJourney->id);

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

            if ($registerJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$REGISTER_ACCOUNT, "POST", [
                "first_name" => $registerJourney->first_name,
                "last_name" => $registerJourney->last_name,
                "phone_number" => $MSISDN,
                "card_number" => $registerJourney->card_number,
                "pin" => $registerJourney->pin,
            ]);

            RegisterUserJourney::destroy($registerJourney->id);

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    public function topUpCardAccount($MSISDN)
    {

        try {

            $topUpUserJourney = TopUpCardUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($topUpUserJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$TOP_UP_CARD, "POST", [
                "txn_amount" => $topUpUserJourney->txn_amount,
                "account_type" => $topUpUserJourney->account_type,
                "phone_number" => $topUpUserJourney->payer_phone_number,
                "card_number" => $topUpUserJourney->card_number,
                "pin" => $topUpUserJourney->pin,
            ]);

            TopUpCardUserJourney::destroy($topUpUserJourney->id);

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }


    public function sendMoney($MSISDN)
    {

        try {

            $sendMoneyUserJourney = SendMoneyUserJourney::where('phone_number', '=', $MSISDN)->first();

            if ($sendMoneyUserJourney == null) {

                return null;
            }

            $requestRes = $this::makeRequest($this::$SEND_MONEY, "POST", [
                "txn_amount" => $sendMoneyUserJourney->txn_amount,
                "account_type" => $sendMoneyUserJourney->account_type,
                "phone_number" => $sendMoneyUserJourney->payer_phone_number,
                "card_number" => $sendMoneyUserJourney->card_number,
                "pin" => $sendMoneyUserJourney->pin,
            ]);

            SendMoneyUserJourney::destroy($sendMoneyUserJourney->id);

            return $requestRes;
        } catch (\Exception $e) {

            return [
                "status" => "error",
                "message" => $e->getMessage()
            ];
        }
    }

    /**
     * Fetches the list of banks from the API and formats it.
     *
     * @return array
     */
    public function fetchBanks()
    {

        try {
            // Make the GET request
            $requestRes = $this::makeRequest($this::$FETCH_BANKS, "GET");

            $formattedBanks = [];
            $index = 1; // Start indexing from 1

            // Process each bank and format the data
            foreach ($requestRes['responseJson'] as $bank) {
                $formattedBanks[$index++] = [
                    'id' => $bank['institution_id'],
                    'name' => $bank['institution_name'],
                ];
            }

            // Return the formatted array
            return $formattedBanks;

        } catch (\Exception $e) {

            return [];
        }
    }
}
