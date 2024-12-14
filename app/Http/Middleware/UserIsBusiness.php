<?php

namespace App\Http\Middleware;

use App\Core\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserIsBusiness
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->is_business) {
            return ResponseHelper::renderCustomErrorResponse([
                'message' => 'payment with cash only for business users',
                'code' => Response::HTTP_UNAUTHORIZED
            ]);
        }
        return $next($request);
    }
}
