<?php

namespace App\Http\Controllers;

use App\Models\CheckBalanceUserJourney;
use App\Models\RegisterUserJourney;
use App\Traits\HeaderUssdMsgTrait;
use App\Traits\HttpUtilsTrait;
use Illuminate\Http\Request;
use App\Traits\UtilsTrait;

class UssdController extends Controller
{
    use UtilsTrait, HttpUtilsTrait, HeaderUssdMsgTrait;

    public function show(Request $request,)
    {
        try {

            $MSISDN = $request->query('MSISDN');
            $SUBSCRIBER_INPUT = $request->query('INPUT');
            $RequestType = $request->query('RequestType');
            $sessionId = $this->generateUniqueString();

            //$this->isValidPersonName('')


            $accResponse = $this->checkAcc($MSISDN);
            $registerJourney = RegisterUserJourney::where('phone_number', '=', $MSISDN)->first();
            $checkBalanceJourney = CheckBalanceUserJourney::where('phone_number', '=', $MSISDN)->first();

            //dd($registerJourney);

            if($RequestType == '1'){

                $options = $this->options(isset($accResponse['error_msg']));

                $response_msg = $this->formatOptionsResponseMsg($options, $this::$WELCOME_MSG);

                return response($response_msg, 200)->header('Auth-key', '');

            }

            //CheckBalanceUserJourney

            if(isset($accResponse['error_msg'])){

                if($SUBSCRIBER_INPUT == '1'){

                    $newRegisterJourney = RegisterUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if($newRegisterJourney == null){

                        return response($this::$ERROR_MSG, 200)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$ENTER_FIRST_NAME);

                    return response($response_msg, 200)->header('Auth-key', '');

                }

                if($registerJourney != null && $registerJourney->first_name == null){

                    $registerJourney->first_name = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_LAST_NAME);

                    return response($response_msg, 200)->header('Auth-key', '');

                }

                if($registerJourney != null && $registerJourney->last_name == null){

                    $registerJourney->last_name = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_CARD_NUMBER);

                    return response($response_msg, 200)->header('Auth-key', '');

                }

                if($registerJourney != null && $registerJourney->card_number == null){

                    $registerJourney->card_number = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_SET_PIN);

                    return response($response_msg, 200)->header('Auth-key', '');

                }

                if($registerJourney != null && $registerJourney->pin == null){

                    $registerJourney->pin = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$SUCCESSFUL_CARD_REGISTRATION_MSG);

                    $card_response = $this->cardAccountRegister($MSISDN);

                    if($card_response['response_msg'] == "Card account with the this card number already exists"){

                        return response()->json([
                            "card_response" => $card_response,
                        ]);

                    }

                    return response($response_msg, 200)->header('Auth-key', '');

                }

                $response_msg = $this->formatResponseMsg($this::$GOOD_BYE_MSG);

                return response($response_msg, 200)->header('Auth-key', '');

            }

            if ($checkBalanceJourney != null || (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '2')) {
                //NOTE: Initialize Topup Card user Journey and all the logic involved

                if ($checkBalanceJourney == null) {

                    $newCheckBalanceJourney = CheckBalanceUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if($newCheckBalanceJourney == null){

                        return response($this::$ERROR_MSG, 200)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg("To check your balance," . $this::$ENTER_PIN);

                    return response($response_msg, 200)->header('Auth-key', '');


                } else {

                    $balance_response = $this->checkBalance($MSISDN, $SUBSCRIBER_INPUT);

                    if($balance_response == null){

                        return response($this::$ERROR_MSG, 200)->header('Auth-key', '');
                    }

                    if(isset($balance_response['error_msg'])){

                        $response_msg = $balance_response['error_msg'];

                        return response($response_msg, 200)->header('Auth-key', '');

                    }

                    $response_msg = $balance_response['response_msg'];

                    return response($response_msg, 200)->header('Auth-key', '');
                }

            }

            if (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '2') {
                //NOTE: Initialize Check Balance user Journey and all the logic involved
            }

            if (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '3') {
                //NOTE: Initialize Send Money user Journey and all the logic involved
            }

            if (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '4') {
                //NOTE: Initialize Pay Marchant  user Journey and all the logic involved
            }

            if (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '5') {
                //NOTE: Initialize Reset Pin user Journey and all the logic involved
            }

            if (!isset($accResponse['error_msg']) && $SUBSCRIBER_INPUT == '6') {
                //NOTE: Initialize Block Card user Journey and all the logic involved
            }



        } catch (\Exception $e) {
            //throw $th;
            return response($e->getMessage(), 200)
                ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');
        }
    }
}
