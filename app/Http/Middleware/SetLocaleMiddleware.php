<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale');

        if (!$locale && Auth::check() && Auth::user()->tenant_id) {
            $cliente = \App\Models\Cliente::find(Auth::user()->tenant_id);
            $locale = ($cliente && $cliente->pais === 'jp') ? 'ja' : 'pt';
        }

        app()->setLocale($locale ?? 'pt');

        return $next($request);
    }
}
