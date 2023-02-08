<?php

namespace App\Http\Middleware;

use App\Http\Resources\AuthApiController;
use Closure;
use Illuminate\Http\Request;

class AuthenticateSeriesResource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     //* @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $header = explode(" ", $request->header('Authorization'));

        $token = hash('SHA256', $header[1]);

        $auth = new AuthApiController();
        $verifiedToken = $auth->verifyUserToken($token);

        return $verifiedToken ? $next($request) : response()->json(['success' => 'false', 'message' => 'invalid or expired token'], 401);
    }
}
