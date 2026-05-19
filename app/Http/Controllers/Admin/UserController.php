<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	/**
	 * Display a listing of the users.
	 */
	public function index()
	{
		// Пагинация с поиском и фильтрацией
		$query = User::with(['profile', 'paymentSettings', 'llmSettings', 'dataSettings']);

		// Поиск по email
		if (request('search')) {
			$query->where('email', 'like', '%' . request('search') . '%');
		}

		// Фильтр по статусу
		if (request('status') !== null) {
			$query->where('is_active', (bool) request('status'));
		}

		// Фильтр по роли администратора
		if (request('role') !== null) {
			$query->where('is_admin', (bool) request('role'));
		}

		$users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

		return view('admin.users.index', compact('users'));
	}

	/**
	 * Show the form for creating a new user.
	 */
	public function create()
	{
		// Загружаем данные для select-полей (если нужны)
		$profiles = \App\Models\Profile::all();
		$paymentSettings = \App\Models\PaymentSetting::all();
		$llmSettings = \App\Models\LlmSetting::all();
		$dataSettings = \App\Models\DataSetting::all();

		return view('admin.users.create', compact(
			'profiles', 'paymentSettings', 'llmSettings', 'dataSettings'
		));
	}

	/**
	 * Store a newly created user in storage.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:8|confirmed',
			'is_active' => 'boolean',
			'is_admin' => 'boolean',

			// Внешние ключи (опционально)
			'profile_id' => 'nullable|exists:profiles,id',
			'payment_settings_id' => 'nullable|exists:payment_settings,id',
			'llm_settings_id' => 'nullable|exists:llm_settings,id',
			'data_settings_id' => 'nullable|exists:data_settings,id',
		]);

		$validated['password'] = Hash::make($validated['password']);

		// Поля статусов по умолчанию, если не переданы
		$validated['is_active'] = $validated['is_active'] ?? true;
		$validated['is_admin'] = $validated['is_admin'] ?? false;

		$user = User::create($validated);

		return redirect()
			->route('admin.users.edit', $user)
			->with('success', 'Пользователь успешно создан.');
	}

	/**
	 * Show the form for editing the specified user.
	 */
	public function edit(User $user)
	{
		$profiles = \App\Models\Profile::all();
		$paymentSettings = \App\Models\PaymentSetting::all();
		$llmSettings = \App\Models\LlmSetting::all();
		$dataSettings = \App\Models\DataSetting::all();

		return view('admin.users.edit', compact(
			'user', 'profiles', 'paymentSettings', 'llmSettings', 'dataSettings'
		));
	}

	/**
	 * Update the specified user in storage.
	 */
	public function update(Request $request, User $user)
	{
		$validated = $request->validate([
			'email' => [
				'required',
				'email',
				Rule::unique('users', 'email')->ignore($user->id),
			],
			'password' => 'nullable|min:8|confirmed',
			'is_active' => 'boolean',
			'is_admin' => 'boolean',

			'profile_id' => 'nullable|exists:profiles,id',
			'payment_settings_id' => 'nullable|exists:payment_settings,id',
			'llm_settings_id' => 'nullable|exists:llm_settings,id',
			'data_settings_id' => 'nullable|exists:data_settings,id',
		]);

		// Хэшируем пароль только если он был передан
		if (!empty($validated['password'])) {
			$validated['password'] = Hash::make($validated['password']);
		} else {
			unset($validated['password']);
		}

		$user->update($validated);

		return redirect()
			->route('admin.users.edit', $user)
			->with('success', 'Данные пользователя обновлены.');
	}

	/**
	 * Remove the specified user from storage.
	 */
	public function destroy(User $user)
	{
		// Защита от удаления самого себя
		if ($user->id === auth()->id()) {
			return back()->with('error', 'Нельзя удалить собственную учетную запись.');
		}

		// Опционально: защита от удаления последнего администратора
		if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
			return back()->with('error', 'Нельзя удалить последнего администратора.');
		}

		$user->delete();

		return redirect()
			->route('admin.users.index')
			->with('success', 'Пользователь удалён.');
	}
}
