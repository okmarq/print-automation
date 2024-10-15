<?php

namespace App\Http\Middleware;

use App\Models\AdminSetting;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class Settings
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!AdminSetting::exists()) return Redirect::route('print.upload')->with('error', 'Admin settings are not available.');
        return $next($request);
    }
}
