<?php
namespace App\Exceptions;

class AppException extends \Exception
{
    protected int $statusCode = 500;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
