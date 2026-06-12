<?php
namespace App\Core;

class Cache
{
    private static string $dir;

    private static function dir(): string
    {
        if (!isset(self::$dir)) {
            self::$dir = __DIR__ . '/../../storage/cache';
            if (!is_dir(self::$dir)) {
                mkdir(self::$dir, 0755, true);
            }
        }
        return self::$dir;
    }

    private static function path(string $key): string
    {
        return self::dir() . '/' . md5($key) . '.cache';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $file = self::path($key);

        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));

        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public static function put(string $key, mixed $value, int $ttl = 3600): void
    {
        $data = [
            'value'   => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
        ];

        file_put_contents(self::path($key), serialize($data), LOCK_EX);
    }

    public static function forget(string $key): void
    {
        $file = self::path($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cached = self::get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        self::put($key, $value, $ttl);
        return $value;
    }
}
