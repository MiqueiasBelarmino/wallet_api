<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\{TokenExpiredException, TokenInvalidException};

class ApiAuthUser extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $exception) {
            if ($exception instanceof TokenInvalidException){
                return response()->json([
                    'message' => 'Token inválido',
                    'status' => env('CODE_AUTH_FAIL')
                ]);
            }else if ($exception instanceof TokenExpiredException){
                return response()->json([
                    'message' => 'Token expirado',
                    'status' => env('CODE_AUTH_FAIL')
                ]);
            }else{
                return response()->json([
                    'message' => 'Token não encontrado',
                    'status' => env('CODE_AUTH_FAIL')
                ]);
            }
        }
        return $next($request);
    }
}