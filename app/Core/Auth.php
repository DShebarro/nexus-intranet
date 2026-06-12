<?php
namespace App\Core;

class Auth
{
    private const SESSION_KEY = 'user';

    public static function startSession(): void
    {
        Csrf::startSession();
    }

    public static function login(array $user): void
    {
        self::startSession();
        unset($user['password']);
        $_SESSION[self::SESSION_KEY] = $user;
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        self::startSession();
        return !empty($_SESSION[self::SESSION_KEY]);
    }

    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user ? (int) $user['id'] : null;
    }

    public static function hasRole(string ...$roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }
        return in_array($user['role'], $roles, true);
    }

    public static function isAdmin(): bool
    {
        return self::hasRole('admin');
    }
}
