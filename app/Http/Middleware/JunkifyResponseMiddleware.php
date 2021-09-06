<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class JunkifyResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);
        $response->setContent(($response->junkify($response->content())));
        return $response;
    }
}
