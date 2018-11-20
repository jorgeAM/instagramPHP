<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      //$token = $request->header('Authorization');
      $token = $request->bearerToken();
      if(!$token){
        return response()->json(['error' => 'Debes estar autenticado'], 401);
      }

      try {
           $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
       } catch(ExpiredException $e) {
         return response()->json(['error' => 'Token ha expirado'], 400);
       } catch(Exception $e) {
         return response()->json(['error' => 'Hubo un error al decodificar el token'], 400);
       }

       $user = User::find($credentials->sub);
       $request->auth = $user;
       return $next($request);
    }
}
