<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\AuthException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (!Auth::check()) {
            if ($request->isAjax() || str_starts_with($request->uri(), '/api/')) {
                (new Response())->json(['error' => 'Não autenticado.'], 401);
                return;
            }
            (new Response())->redirect('/login');
            return;
        }

        $next($request);
    }
}
