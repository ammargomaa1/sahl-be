<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeTrueAndFalseStringToBoolean extends TransformsRequest
{
    protected static $skipCallbacks = [];
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

    protected function transform($key, $value)
    {
        if ($value === 'true') {
            $value = true;
        }elseif ($value === 'false') {
            $value = false;
        }
        return $value;
    }
}
