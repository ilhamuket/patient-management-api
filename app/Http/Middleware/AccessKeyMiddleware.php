<?php

namespace App\Http\Middleware;

use App\Models\AccessKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessKey = $request->header('X-API-KEY');

        if (!$accessKey) {
            return response()->json([
                'success' => false,
                'message' => 'Access key is required',
            ], 401);
        }

       $key = AccessKey::where('key', $accessKey)
        ->where('is_active', true)
        ->first();



        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid access key',
            ], 401);
        }

        return $next($request);
    }
}