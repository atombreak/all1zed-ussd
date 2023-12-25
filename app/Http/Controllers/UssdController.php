<?php

namespace App\Http\Controllers;

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

            //dd($registerJourney);

            if($RequestType == '1'){

                $options = $this->options(isset($accResponse['error_msg']));

                $response_msg = $this->formatOptionsResponseMsg($options, $this::$WELCOME_MSG);

                return response($response_msg, 200)->header('Auth-key', '');

            }

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

                $response_msg = $this->formatResponseMsg($this::$ENTER_FIRST_NAME);

                return response($response_msg, 200)->header('Auth-key', '');

            }

        } catch (\Exception $e) {
            //throw $th;
            return response($e->getMessage(), 200)
                ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');
        }
    }
}
