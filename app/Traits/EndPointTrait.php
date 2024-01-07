<?php

namespace App\Traits;

trait EndPointTrait
{
    public static string $PIN_RESET = '/accounts/card/account/pin/reset';

    public static string $MERCHANT_PAY = '/payments/merchant/pay/';

    public static string $CHECK_ACCOUNT = '/api/cards';

    public static string $CHECK_BALANCE = '/accounts/card/balance/';

    public static string $BLOCK_CARD = '/accounts/card/deactivate/';

    public static string $TOP_UP_CARD = '/accounts/card/topup';

    public static string $REGISTER_ACCOUNT = '/accounts/card_account/register/';

    public static string $SEND_MONEY = '/payments/cashin/';

    public static string $FETCH_BANKS = '/accounts/banks/list';

}
