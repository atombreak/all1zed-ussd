<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UssdController extends Controller
{
    public function generateUniqueString()
    {
        return Str::random(22) . now()->timestamp;
    }


    public function show(Request $request,)
    {
        try {

            $MSISDN = $request->query('MSISDN');
        $SUBSCRIBER_INPUT = $request->query('INPUT');
        $RequestType = $request->query('RequestType');
        $sessionId = $this->generateUniqueString();

        $options = [
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
              "name" => "Resend-Pin",
            ],
            [
              "id" => 6,
              "name" => "Block Card",
            ],
            [
              "id" => 7,
              "name" => "Register Card",
            ],
            [
              "id" => 8,
              "name" => "Buy Insurance",
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ',
            'Content-Type' => 'application/json',
        ])->post('https://all1zedmoney.com/', [
            'phone_numer' => $MSISDN,
        ]);

        // Accessing the response body as an array
        $responseBody = $response->json();

        if($responseBody['id'] != null){
            $options[] = (object) [
                'id' => 1,
                'name' => 'Check Balance',
                'url' => 'https://all1zedmoney.com/',
            ];

        }

        $menu_options = [];
        foreach ($options as $option) {
            $menu_options[] = $option->id . '.' . ' ' . $option->name;
        }

        $response_msg = 'Welcome to All1Zed Cards.' . "\n";

        foreach ($menu_options as $key => $value) {
            $response_msg .= "{$value} \n";
        }
        $response_msg = '';

        return response($response_msg, 200)
            ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');

        } catch (\Exception $e) {
            //throw $th;
            return response($e->getMessage(), 200)
            ->header('Auth-key', 'Bearer dfvlsdflkvmkdmfdfvS+EDwJNKLLDFPF');
        }
    }
}
