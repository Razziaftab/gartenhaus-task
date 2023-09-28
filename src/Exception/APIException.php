<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class APIException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct();
    }
}