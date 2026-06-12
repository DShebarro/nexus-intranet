<?php
namespace App\Exceptions;

class NotFoundException extends AppException
{
    protected int $statusCode = 404;

    public function __construct(string $message = 'Recurso não encontrado.')
    {
        parent::__construct($message);
    }
}
