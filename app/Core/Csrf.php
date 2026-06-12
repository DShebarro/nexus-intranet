<?php
namespace App\Core;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }
    }

    public static function token(): string
    {
        self::startSession();

        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function meta(): string
    {
        return '<meta name="csrf-token" content="' . e(self::token()) . '">';
    }

    public static function validate(?string $token = null): bool
    {
        self::startSession();

        $token ??= $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_POST['_csrf']
            ?? null;

        if (!$token || empty($_SESSION[self::TOKEN_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function validateOrFail(Request $request): void
    {
        $token = $request->header('X-CSRF-Token')
            ?? $request->post('_csrf')
            ?? ($request->json()['_csrf'] ?? null);

        if (!self::validate($token)) {
            throw new \App\Exceptions\ForbiddenException('Token CSRF inválido.');
        }
    }
}
