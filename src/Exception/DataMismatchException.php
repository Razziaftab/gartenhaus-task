<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DataMismatchException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Currency ISO code not found', Response::HTTP_FORBIDDEN);
    }
}