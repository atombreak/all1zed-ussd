<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait CheckAccTrait {

    public function checkAcc($MSISDN){

        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ',
                'Content-Type' => 'application/json',
            ])->get('http://cards_system.all1zedmoney.com/api/cards', [
                'phone_numer' => $MSISDN,
            ]);

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

}
