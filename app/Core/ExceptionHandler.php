<?php
namespace App\Core;

use App\Exceptions\AppException;
use App\Exceptions\ValidationException;
use Throwable;

class ExceptionHandler
{
    public static function register(bool $debug = false): void
    {
        set_exception_handler(fn(Throwable $e) => self::handle($e, $debug));
        set_error_handler(function (int $severity, string $message, string $file, int $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });
    }

    public static function handle(Throwable $e, bool $debug = false): void
    {
        $response = new Response();

        if ($e instanceof ValidationException) {
            $response->json([
                'error'  => $e->getMessage(),
                'errors' => $e->errors,
            ], $e->getStatusCode());
            return;
        }

        if ($e instanceof AppException) {
            $response->json(['error' => $e->getMessage()], $e->getStatusCode());
            return;
        }

        if ($debug) {
            $response->json([
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => explode("\n", $e->getTraceAsString()),
            ], 500);
            return;
        }

        error_log("[Nexus] {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}");
        $response->json(['error' => 'Erro interno do servidor.'], 500);
    }
}
