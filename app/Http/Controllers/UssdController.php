<?php

namespace App\Http\Controllers;

use App\Traits\HttpUtilsTrait;
use Illuminate\Http\Request;
use App\Traits\UtilsTrait;

class UssdController extends Controller
{
    use UtilsTrait, HttpUtilsTrait;

    public function show(Request $request,)
    {
        try {

            $MSISDN = $request->query('MSISDN');
            $SUBSCRIBER_INPUT = $request->query('INPUT');
            $RequestType = $request->query('RequestType');
            $sessionId = $this->generateUniqueString();

            $accResponse = $this->checkAcc($MSISDN);

            $options = $this->options(isset($accResponse['error_msg']));


            $menu_options = [];
            foreach ($options as $option) {
                $menu_options[] = $option['id'] . '.' . ' ' . $option['name'];
            }

            $response_msg = 'Welcome to All1Zed Cards.' . "\n";

            foreach ($menu_options as $key => $value) {
                $response_msg .= "{$value} \n";
            }

            return response($response_msg, 200)->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');

        } catch (\Exception $e) {
            //throw $th;
            return response($e->getMessage(), 200)
                ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');
        }
    }
}
