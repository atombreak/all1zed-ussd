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
            self::BLOCK_CARD => 'Block Card',
            self::PIN_RESET => 'Reset Card Pin',
            self::CHECK_ACCOUNT => 'Check if Card Account Exists',
            self::MERCHANT_PAY => 'Merchant Paying',
        };
    }

}
