<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = null;

        // 1. Check if user is logged in and has a tenant_id
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenantId = Auth::user()->tenant_id;
        }

        // 2. Check if the URL has a slug (evaluation page)
        $slug = $request->route('slug');
        if ($slug) {
            $cliente = Cliente::where('slug', $slug)->first();
            if ($cliente) {
                $tenantId = $cliente->id;
            }
        }

        // 3. Fallback to route parameters if 'cliente' is passed (dashboard routes)
        if (!$tenantId && $request->route('cliente')) {
            $tenantId = $request->route('cliente');
        }

        if ($tenantId) {
            app()->instance('tenant_id', $tenantId);
        }

        return $next($request);
    }
}
