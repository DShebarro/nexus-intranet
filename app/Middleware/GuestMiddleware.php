<?php
namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (Auth::check()) {
            (new Response())->redirect('/dashboard');
            return;
        }

        $next($request);
    }
}
