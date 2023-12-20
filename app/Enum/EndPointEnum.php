<?php

namespace App\Enums;

enum EndPointEnum: string
{
    case PIN_RESET = '/accounts/card/account/pin/reset';

    case MERCHANT_PAY = '/payments/merchant/pay/';

    case CHECK_ACCOUNT = '/api/cards';

    case BLOCK_CARD = '/accounts/card/deactivate/';

    public function getLabel(): string
    {
        return match ($this) {
            self::BLOCK_CARD => '/accounts/card/deactivate/',
            self::PIN_RESET => '/accounts/card/deactivate/',
            self::CHECK_ACCOUNT => '/api/cards',
            self::MERCHANT_PAY => '/payments/merchant/pay/',
        };
    }

}
