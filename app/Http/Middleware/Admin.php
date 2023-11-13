<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current logged in user
        $user = $request->user();
        
        if ($user->user_type != "admin") {
            $request->user()->tokens()->delete();
            $response = [
                "message" => "Forbidden Access"
            ];
            return response($response,403);
        }

        return $next($request);
    }
}
