// app/Http/Middleware/AuthStub.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AuthStub
{
	public function handle(Request $request, Closure $next)
	{
		// ⚠️ ТОЛЬКО ДЛЯ LOCAL! Никогда не используйте в продакшене.
		if (app()->environment('local')) {
			// Автоматически "входим" как первый админ или пользователь ID=1
			if (!auth()->check()) {
				$user = User::where('is_admin', true)->first()
				        ?? User::first();

				if ($user) {
					auth()->login($user);
				}
			}
		}

		return $next($request);
	}
}