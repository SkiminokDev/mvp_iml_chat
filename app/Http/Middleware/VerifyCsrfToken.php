<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	/**
	 * URI, которые не требуют CSRF-токена
	 */
	protected $except = [
		'v1/*',           // 🔥 Все API-маршруты
		'api/*',
		//'user/messages/*', // Или конкретно ваши
	];
}