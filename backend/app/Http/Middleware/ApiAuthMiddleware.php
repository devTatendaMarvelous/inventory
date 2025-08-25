<?php

namespace App\Http\Middleware;


use App\Traits\HasApiResponses;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Gate;

class ApiAuthMiddleware
{
    use HasApiResponses;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            dd('test');
            JWTAuth::parseToken()->authenticate();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->unauthorizedResponseHandler('Invalid Token');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->unauthorizedResponseHandler('Token Expired');
        } catch (Exception $e) {
            return $this->unauthorizedResponseHandler('Token not found');
        }
        return $next($request);
    }
}
