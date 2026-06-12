<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\ForbiddenException;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (!Auth::isAdmin()) {
            if ($request->isAjax() || str_starts_with($request->uri(), '/api/')) {
                (new Response())->json(['error' => 'Acesso negado. Requer perfil admin.'], 403);
                return;
            }
            throw new ForbiddenException('Acesso negado. Requer perfil admin.');
        }

        $next($request);
    }
}
