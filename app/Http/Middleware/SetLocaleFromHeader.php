<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        if ($request->header('Accept-Language')) {
            $locale = $request->header('Accept-Language');
            if (in_array($locale, ['en', 'ar'])) {
                App::setLocale($locale);
            }
        }
        return $next($request);
    }
}
