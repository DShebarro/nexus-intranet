<?php
namespace App\Exceptions;

class ForbiddenException extends AppException
{
    protected int $statusCode = 403;

    public function __construct(string $message = 'Acesso negado.')
    {
        parent::__construct($message);
    }
}
