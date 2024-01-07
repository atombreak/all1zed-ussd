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
    public static string $ENTER_SET_PIN = 'Finally set a 4 digit pin for your new card';

    // Card Pin set Header messages
    public static string $ENTER_PIN = 'Please enter your 4 digit pin';

    //Top Up Card Header Messgaes
    public static string $TOP_UP_ENTER_CARD_NUMBER = "Please enter the recepient's card number";
    public static string $TOP_UP_PAYER_MOMO = "Enter the payer's Mobile Money number";
    public static string $TOP_UP_AMOUNT = "Kindly enter amount to be deposited";


    // Reset Pin Header Messages
    public static string $ENTER_CURRENT_PIN = "Please enter your current pin";
    public static string $ENTER_NEW_PIN = "Please enter your new 4 digit pin";

    // Block Card Header Messages
    public static string $ENTER_BLOCK_REASON = "Enter the reason for blocking the card";

    // Pay Merchant Header Messages
    public static string $ENTER_MERCHANT_CODE = "Please enter the merchant code";
    public static string $ENTER_AMOUNT = "Please enter the amount to be paid";

    // Send Money User Journey
    public static string $CHOOSE_PAYMENT_TYPE = "Choose where you want to send to:";
    public static string $ENTER_RECEIVER_CARD_NUMBER = "Please enter the receiver's card number";
    public static string $SELECT_BANK = "Please select bank receiving";

}
