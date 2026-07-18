<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Skip noise: assets, service worker, manifests, the activity page itself
            $path = '/' . ltrim($request->path(), '/');
            $skip = ['/sw.js', '/manifest.json', '/manifest-retailer.json', '/favicon.ico'];
            if (in_array($path, $skip, true)
                || str_starts_with($path, '/build/')
                || str_starts_with($path, '/icons/')
                || str_starts_with($path, '/storage/')
                || str_starts_with($path, '/activity')) {
                return $response;
            }

            $actorType = 'guest';
            $actorName = null;

            if (Auth::guard('web')->check()) {
                $actorType = 'owner';
                $actorName = Auth::guard('web')->user()->name;
            } elseif ($request->bearerToken() && ($u = Auth::guard('sanctum')->user())) {
                $actorType = 'partner';
                $actorName = $u->firm_name ?? ('#' . $u->id);
            }

            DB::table('activity_log')->insert([
                'actor_type' => $actorType,
                'actor_name' => $actorName,
                'method' => $request->method(),
                'path' => substr($path . ($request->getQueryString() ? '?' . $request->getQueryString() : ''), 0, 500),
                'route_name' => optional($request->route())->getName(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the app.
        }

        return $response;
    }
}