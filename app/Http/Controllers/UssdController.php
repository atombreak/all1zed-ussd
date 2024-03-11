<?php

namespace App\Http\Controllers;

use App\Models\BlockCardUserJourney;
use App\Models\CheckBalanceUserJourney;
use App\Models\PayMerchantUserJourney;
use App\Models\RegisterUserJourney;
use App\Models\ResetPinUserJourney;
use App\Models\SendMoneyUserJourney;
use App\Models\TopUpCardUserJourney;
use App\Models\InsuranceUserJourney;
use App\Traits\HeaderUssdMsgTrait;
use App\Traits\HttpUtilsTrait;
use App\Traits\StatusCodeTrait;
use Illuminate\Http\Request;
use App\Traits\UtilsTrait;

class UssdController extends Controller
{
    use UtilsTrait, HttpUtilsTrait, HeaderUssdMsgTrait, StatusCodeTrait;

    public function show(Request $request,)
    {
        try {

            $MSISDN = $request->query('MSISDN');
            $SUBSCRIBER_INPUT = $request->query('INPUT');
            $RequestType = $request->query('RequestType');
            $sessionId = $this->generateUniqueString();

            $accResponse = $this->checkAcc($MSISDN);
            $registerJourney = RegisterUserJourney::where('phone_number', '=', $MSISDN)->first();
            $checkBalanceJourney = CheckBalanceUserJourney::where('phone_number', '=', $MSISDN)->first();
            $topUpUserJourney = TopUpCardUserJourney::where('phone_number', '=', $MSISDN)->first();
            $resetPinUserJourney = ResetPinUserJourney::where('phone_number', '=', $MSISDN)->first();
            $blockCardUserJourney = BlockCardUserJourney::where('phone_number', '=', $MSISDN)->first();
            $payMerchantJourney = PayMerchantUserJourney::where('phone_number', '=', $MSISDN)->first();
            $sendMoneyUserJourney = SendMoneyUserJourney::where('phone_number', '=', $MSISDN)->first();
            $insuranceTypeJourney = InsuranceUserJourney::where('phone_number', '=', $MSISDN)->first();

            //dd((int) 1 == (string) '1');
            $NotSendMoney = $sendMoneyUserJourney == null;

            //dd($accResponse);

            if ($RequestType == '1') {

                #$options = $this->options($accResponse["statusCode"] != $this::$STATUS_OK);
                $options = $this->options(true);

                $response_msg = $this->formatOptionsResponseMsg($options, $this::$WELCOME_MSG);

                return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
            }

            if($insuranceTypeJourney == null && $RequestType == '2' && $SUBSCRIBER_INPUT == '2'){

                $response_msg = $this->formatOptionsResponseMsg($this->InsuranceOptions(), $this::$INSURANCE_WELCOME_MESSAGE);
                
                $newInsuranceUserJourney = InsuranceUserJourney::create([
                    'phone_number' => $MSISDN
                ]);
                
                return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
            }

            if(true && $insuranceTypeJourney != null){

                if ($SUBSCRIBER_INPUT == '5' && $insuranceTypeJourney->step == 1){
                    
                    $response_msg = $this->formatResponseMsg($this::$INSURANCE_CLAIM_MESSAGE);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

            }
 
            //CheckBalanceUserJourney
            if ($accResponse["statusCode"] == $this::$STATUS_NOT_FOUND) {

                if ($SUBSCRIBER_INPUT == '1') {

                    $newRegisterJourney = RegisterUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newRegisterJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$ENTER_FIRST_NAME);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($registerJourney != null && $registerJourney->first_name == null) {

                    $registerJourney->first_name = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_LAST_NAME);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($registerJourney != null && $registerJourney->last_name == null) {

                    $registerJourney->last_name = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_CARD_NUMBER);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($registerJourney != null && $registerJourney->card_number == null) {

                    $registerJourney->card_number = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_SET_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($registerJourney != null && $registerJourney->pin == null) {

                    $registerJourney->pin = $SUBSCRIBER_INPUT;
                    $registerJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$SUCCESSFUL_CARD_REGISTRATION_MSG);

                    $card_response = $this->cardAccountRegister($MSISDN);

                    if ($card_response == null || $card_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($card_response['statusCode'] != $this::$STATUS_CREATED) {

                        $response_msg = $card_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                $response_msg = $this->formatResponseMsg($this::$GOOD_BYE_MSG);

                return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
            }

            //Send Money user journey
            if ($sendMoneyUserJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '1')) {

                if ($sendMoneyUserJourney == null) {

                    $newSendMoneyUserJourney = SendMoneyUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newSendMoneyUserJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatOptionsResponseMsg($this->TransactionType(), $this::$CHOOSE_PAYMENT_TYPE);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                /*
                                NOTE::Send money using card code section
                            */

                if ($sendMoneyUserJourney->phone_number != null && $sendMoneyUserJourney->step == 1 && $SUBSCRIBER_INPUT == '1') {

                    $sendMoneyUserJourney->account_type = 'card';
                    $sendMoneyUserJourney->step = 2;
                    $sendMoneyUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_RECEIVER_CARD_NUMBER);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }


                if ($sendMoneyUserJourney->account_type != null && $sendMoneyUserJourney->account_type == 'card' && $sendMoneyUserJourney->step == 2) {

                    $sendMoneyUserJourney->receiver_card_number = $SUBSCRIBER_INPUT;
                    $sendMoneyUserJourney->step = 3;
                    $sendMoneyUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$SENDING_AMOUNT);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }


                if ($sendMoneyUserJourney->account_type == 'card' && $sendMoneyUserJourney->receiver_card_number != null && $sendMoneyUserJourney->step == 3) {

                    $sendMoneyUserJourney->txn_amount = $SUBSCRIBER_INPUT;
                    $sendMoneyUserJourney->step = 4;
                    $sendMoneyUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($sendMoneyUserJourney->txn_amount != null && $sendMoneyUserJourney->pin == null) {

                    $sendMoneyUserJourney->pin = $SUBSCRIBER_INPUT;
                    $sendMoneyUserJourney->save();

                    $send_money_response = $this->sendMoney($MSISDN);

                    //dd($send_money_response);

                    // if(1 + 1 == 2){

                    //     return response()->json([
                    //         "send_money_response" => $send_money_response,
                    //     ]);

                    // }

                    if ($send_money_response == null || $send_money_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($send_money_response['statusCode'] != $this::$STATUS_OK) {

                        $response_msg = $send_money_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $send_money_response['responseJson']['response'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }


                /*
                                NFS Money transfer code section
                             */
                if ($sendMoneyUserJourney->phone_number != null && $SUBSCRIBER_INPUT == '2') {

                    $sendMoneyUserJourney->account_type = 'nfs';
                    $sendMoneyUserJourney->step = 2;
                    $sendMoneyUserJourney->save();

                    //Fetch available banks from the api
                    $banksFetched = $this->fetchBanks();

                    $response_msg = $this->formatOptionsResponseMsg($banksFetched, $this::$SELECT_BANK);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($sendMoneyUserJourney->account_type != null && $sendMoneyUserJourney->account_type == 'nfs' && $sendMoneyUserJourney->step == 2) {

                    $banksFetched = $this->fetchBanks();

                    try {

                        $userSelection = intval($SUBSCRIBER_INPUT);

                        if ($userSelection > count($banksFetched) || $userSelection <= 0) {

                            $response_msg = $this->formatOptionsResponseMsg($banksFetched, $this::$INVALID_OPTION . "\n" . $this::$SELECT_BANK);
                            return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                        }

                        $sendMoneyUserJourney->institution_name = $banksFetched[$userSelection]['name'];
                        $sendMoneyUserJourney->step = 3;
                        $sendMoneyUserJourney->save();

                        $response_msg = $this->formatResponseMsg($this::$ENTER_BANK_REFERENCE);

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    } catch (\Exception $e) {

                        $response_msg = $this->formatOptionsResponseMsg($banksFetched, $this::$INVALID_OPTION . "\n" . $this::$SELECT_BANK);
                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }
                }

                if ($sendMoneyUserJourney->institution_name != null && $sendMoneyUserJourney->reference == null) {

                    $sendMoneyUserJourney->reference = $SUBSCRIBER_INPUT;
                    $sendMoneyUserJourney->step = 4;
                    $sendMoneyUserJourney->save();

                    //Fetch available banks from the api
                    $banksFetched = $this->fetchBanks();

                    $response_msg = $this->formatResponseMsg($this::$SENDING_AMOUNT);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }


                if ($sendMoneyUserJourney->reference != null && $sendMoneyUserJourney->txn_amount == null) {

                    $sendMoneyUserJourney->txn_amount = $SUBSCRIBER_INPUT;
                    $sendMoneyUserJourney->save();

                    //Fetch available banks from the api
                    $banksFetched = $this->fetchBanks();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }
            }

            if ($topUpUserJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '2')) {
                //NOTE: Initialize Top Up user Journey and all the logic involved

                if ($topUpUserJourney == null) {

                    $newTopUpUserJourney = TopUpCardUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newTopUpUserJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$TOP_UP_ENTER_CARD_NUMBER);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($topUpUserJourney->phone_number != null && $topUpUserJourney->card_number == null) {

                    $topUpUserJourney->card_number = $SUBSCRIBER_INPUT;
                    $topUpUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$TOP_UP_PAYER_MOMO);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($topUpUserJourney->card_number != null && $topUpUserJourney->payer_phone_number == null) {

                    $topUpUserJourney->payer_phone_number = $SUBSCRIBER_INPUT;
                    $topUpUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$TOP_UP_AMOUNT);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }



                if ($topUpUserJourney->payer_phone_number != null && $topUpUserJourney->txn_amount == null) {

                    $topUpUserJourney->txn_amount = $SUBSCRIBER_INPUT;
                    $topUpUserJourney->save();

                    $top_up_response = $this->topUpCardAccount($MSISDN);

                    // dd($top_up_response);

                    // if(1 + 1 == 2){

                    //     return $top_up_response;
                    // }

                    if ($top_up_response == null || $top_up_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($top_up_response['statusCode'] != $this::$STATUS_OK) {

                        $response_msg = $top_up_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $top_up_response['responseJson']['response_msg'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }



                // if ($topUpUserJourney->txn_amount != null && $topUpUserJourney->pin == null) {

                //     $topUpUserJourney->pin = $SUBSCRIBER_INPUT;
                //     $topUpUserJourney->save();

                //     $top_up_response = $this->topUpCardAccount($MSISDN);

                //     // dd($top_up_response);

                //     // if(1 + 1 == 2){

                //     //     return $top_up_response;
                //     // }

                //     if ($top_up_response == null || $top_up_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                //         return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                //     }

                //     if ($top_up_response['statusCode'] != $this::$STATUS_OK) {

                //         $response_msg = $top_up_response['responseJson']['error_msg'];

                //         return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                //     }

                //     $response_msg = $top_up_response['responseJson']['response_msg'];

                //     return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                // }
            }

            //Check balance user journey
            if ($NotSendMoney && $checkBalanceJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '3')) {

                if ($checkBalanceJourney == null) {

                    $newCheckBalanceJourney = CheckBalanceUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newCheckBalanceJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg("To check your balance," . $this::$ENTER_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                } else {

                    $balance_response = $this->checkBalance($MSISDN, $SUBSCRIBER_INPUT);

                    if ($balance_response == null || $balance_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($balance_response['statusCode'] != $this::$STATUS_OK) {

                        $response_msg = $balance_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $balance_response['responseJson']['response_msg'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }
            }


            //Pay Merchant user journey
            if ($payMerchantJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '4')) {

                if ($payMerchantJourney == null) {

                    $newPayMerchantJourney = PayMerchantUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newPayMerchantJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$ENTER_MERCHANT_CODE);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($payMerchantJourney->phone_number != null && $payMerchantJourney->merchant_code == null) {

                    $payMerchantJourney->merchant_code = $SUBSCRIBER_INPUT;
                    $payMerchantJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_AMOUNT);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($payMerchantJourney->merchant_code != null && $payMerchantJourney->txn_amount == null) {

                    $payMerchantJourney->txn_amount = $SUBSCRIBER_INPUT;
                    $payMerchantJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($payMerchantJourney->txn_amount != null && $payMerchantJourney->pin == null) {

                    $payMerchantJourney->pin = $SUBSCRIBER_INPUT;
                    $payMerchantJourney->save();

                    $pay_merchant_response = $this->merchantPay($MSISDN);

                    //dd($reset_pin_response);

                    // if(1 + 1 == 2){

                    //     return response()->json([
                    //         "reset_pin_response" => $reset_pin_response,
                    //     ]);

                    // }

                    if ($pay_merchant_response == null || $pay_merchant_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($pay_merchant_response['statusCode'] != $this::$STATUS_OK) {

                        $response_msg = $pay_merchant_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $pay_merchant_response['responseJson']['response_msg'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }
            }


            //Reset Pin user journey
            if ($resetPinUserJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '5')) {

                if ($resetPinUserJourney == null) {

                    $newResetPinUserJourney = ResetPinUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newResetPinUserJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$ENTER_CURRENT_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($resetPinUserJourney->phone_number != null && $resetPinUserJourney->current_pin == null) {

                    $resetPinUserJourney->current_pin = $SUBSCRIBER_INPUT;
                    $resetPinUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_NEW_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($resetPinUserJourney->current_pin != null && $resetPinUserJourney->new_pin == null) {

                    $resetPinUserJourney->new_pin = $SUBSCRIBER_INPUT;
                    $resetPinUserJourney->save();

                    $reset_pin_response = $this->pinReset($MSISDN);

                    //dd($reset_pin_response);

                    // if(1 + 1 == 2){

                    //     return response()->json([
                    //         "reset_pin_response" => $reset_pin_response,
                    //     ]);

                    // }

                    if ($reset_pin_response == null || $reset_pin_response['statusCode'] == $this::$STATUS_SEVER_ERROR) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($reset_pin_response['statusCode'] != $this::$STATUS_OK) {

                        $response_msg = $reset_pin_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $reset_pin_response['responseJson']['response_msg'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }
            }


            //Block Card user journey
            if ($blockCardUserJourney != null || ($accResponse['statusCode'] == $this::$STATUS_OK && $SUBSCRIBER_INPUT == '6')) {

                if ($blockCardUserJourney == null) {

                    $newResetPinUserJourney = BlockCardUserJourney::create([
                        'phone_number' => $MSISDN
                    ]);

                    if ($newResetPinUserJourney == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $this->formatResponseMsg($this::$ENTER_BLOCK_REASON);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($blockCardUserJourney->phone_number != null && $blockCardUserJourney->reason == null) {

                    $blockCardUserJourney->reason = $SUBSCRIBER_INPUT;
                    $blockCardUserJourney->save();

                    $response_msg = $this->formatResponseMsg($this::$ENTER_PIN);

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }

                if ($blockCardUserJourney->reason != null && $blockCardUserJourney->pin == null) {

                    $blockCardUserJourney->pin = $SUBSCRIBER_INPUT;
                    $blockCardUserJourney->save();

                    $block_card_response = $this->blockCard($MSISDN);

                    //dd($block_card_response);

                    // if(1 + 1 == 2){

                    //     return response()->json([
                    //         "block_card_response" => $block_card_response,
                    //     ]);

                    // }

                    if ($block_card_response == null) {

                        return response($this::$ERROR_MSG, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    if ($block_card_response["statusCode"] != $this::$STATUS_OK) {

                        $response_msg = $block_card_response['responseJson']['error_msg'];

                        return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                    }

                    $response_msg = $block_card_response['responseJson']['Info'];

                    return response($response_msg, $this::$STATUS_OK)->header('Auth-key', '');
                }
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
                $blockCardUserJourney;
            }
        } catch (\Exception $e) {
            //throw $th;
            return response($e->getMessage(), $this::$STATUS_OK)
                ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');
        }
    }
}
