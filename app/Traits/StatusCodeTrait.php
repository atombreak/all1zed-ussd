<?php

namespace App\Traits;

trait StatusCodeTrait
{
    public static int $STATUS_OK = 200;
    public static int $STATUS_CREATED = 201;
    public static int $STATUS_NOT_FOUND = 404;

    public static int $STATUS_UNATHORISED = 401;

    public static int $STATUS_INVALID_REQUEST = 400;

    public static int $STATUS_EXISTS = 409;

    public static int $STATUS_SEVER_ERROR = 500;

}
