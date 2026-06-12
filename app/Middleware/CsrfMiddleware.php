<?php
namespace App\Middleware;

use App\Core\Csrf;
use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware implements MiddlewareInterface
{
    private const SAFE_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request, callable $next): void
    {
        if (in_array($request->method(), self::SAFE_METHODS, true)) {
            $next($request);
            return;
        }

        $token = $request->header('X-CSRF-Token')
            ?? $request->post('_csrf');

        if (!$token) {
            $json = $request->json();
            $token = $json['_csrf'] ?? null;
        }

        if (!Csrf::validate($token)) {
            (new Response())->json(['error' => 'Token CSRF inválido.'], 403);
            return;
        }

        $next($request);
    }
}
