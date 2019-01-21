<?php

namespace App\Http\Middleware;

use App\Helpers\APIResponse;
use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class Jwt Middleware
 *
 * @package \App\Http\Middleware
 */
class JwtMiddleware
{
    use APIResponse;

    /**
     * Handle jwt
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $this->parseAuthHeader($request);
        } catch(Exception $ex) {
            $token = $request->get('token');
        }

        if(empty($token)) {
            return $this->respondWithUnauthorized([],'Token not provided');
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        } catch(ExpiredException $ex) {
            return $this->respondWithBadRequest([], 'Provided token is expired');
        } catch(Exception $ex) {
            return $this->respondWithBadRequest(['error' => $ex->getMessage()], 'An error occurred while decoding the token');
        }

        $user = User::find($credentials->sub);

        if(empty($user)) {
            return $this->respondWithBadRequest([], 'An error occurred while retrieving the user');
        }

        $request->auth = $user;

        return $next($request);
    }

    /**
     * Parse auth header
     *
     * @param \Illuminate\Http\Request $request
     * @param string $header
     * @param string $method
     *
     * @return string
     * @throws Exception
     */
    protected function parseAuthHeader(Request $request, $header = 'authorization', $method = 'bearer'): ?string
    {
        $header = $request->headers->get($header);

        if(!starts_with(strtolower($header), $method)) {
            throw new Exception('Missing auth header');
        }

        $result = trim(str_ireplace($method, '', $header));

        // @TODO Fix this
        if($result == 'null') {
            $result = null;
        }

        return $result;
    }
}