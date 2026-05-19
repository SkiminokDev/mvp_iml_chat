<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DebugSanctum
{
	public function handle(Request $request, Closure $next)
	{
		\Log::info('Sanctum Debug', [
			'url' => $request->fullUrl(),
			'method' => $request->method(),
			'headers' => $request->headers->all(),
			'token' => $request->bearerToken(),
		]);

		return $next($request);
	}
}
