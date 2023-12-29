<?php

namespace App\Traits;

trait HeaderUssdMsgTrait
{
    // Welcome message
    public static string $WELCOME_MSG = 'Welcome to All1Zed Cards.';

    //Thank you message
    public static string $GOOD_BYE_MSG = 'Thank you for using All1Zed Cards.';

    //Successful card registration message
    public static string $SUCCESSFUL_CARD_REGISTRATION_MSG = 'Your card has been successfully registered. Thank you for joining to All1Zed Cards';

    //Error Message
    public static string $ERROR_MSG = 'There was an error processing your query. please try again later';

    // Register User Header messages
    public static string $ENTER_FIRST_NAME = 'Please enter your first name';
    public static string $ENTER_LAST_NAME = 'Enter your last name';
    public static string $ENTER_CARD_NUMBER = 'Please enter the provided card number';
    public static string $ENTER_PIN = 'Finally set a 4 digit pin for your new card';

}
