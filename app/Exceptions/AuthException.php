<?php
namespace App\Exceptions;

class AuthException extends AppException
{
    protected int $statusCode = 401;

    public function __construct(string $message = 'Não autenticado.')
    {
        parent::__construct($message);
    }
}
