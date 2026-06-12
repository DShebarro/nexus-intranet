<?php
namespace App\Exceptions;

class ValidationException extends AppException
{
    protected int $statusCode = 422;

    public function __construct(
        public readonly array $errors,
        string $message = 'Erro de validação.'
    ) {
        parent::__construct($message);
    }
}
