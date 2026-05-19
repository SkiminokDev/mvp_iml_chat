<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
	public function ping()
	{
		return response()->json(['status' => 'ping ok']);
	}

	public function user(Request $request)
	{
		try {
			$user = $request->user();

			if (!$user) {
				return response()->json(['error' => 'User not found'], 401);
			}

			// Логируем для отладки
			\Log::info('User requested', ['user_id' => $user->id]);

			return response()->json([
				'status' => 'auth ok',
				'user' => [
					'id' => $user->id,
					'email' => $user->email,
				]
			]);
		} catch (\Exception $e) {
			\Log::error('Error in user endpoint', [
				'message' => $e->getMessage(),
				'trace' => $e->getTraceAsString()
			]);

			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
}
