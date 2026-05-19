<?php
//// test-db.php
//
//require __DIR__.'/vendor/autoload.php';
//$app = require_once __DIR__.'/bootstrap/app.php';
//$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
//
//use App\Models\Chat;
//
//// Найти чат
//$chat = Chat::find(3);
//echo "Найден чат: " . ($chat?->title ?? 'Нет') . PHP_EOL;
//
//// Обновить
//if ($chat) {
//	$chat->user_id = 2;
//	$chat->save();
//	echo "Чат обновлён!" . PHP_EOL;
//}
//
//// Показать все чаты
//echo "\nВсе чаты:" . PHP_EOL;
//Chat::all()->each(fn($c) => print("ID: $c->id | Title: $c->title\n"));

// create-token.php

// 🔹 Подключаем автозагрузчик Laravel
require __DIR__ . '/vendor/autoload.php';

// 🔹 Инициализируем приложение
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 🔹 Получаем первого пользователя (или укажите ID)
$user = \App\Models\User::first(); // или User::find(1);

if (!$user) {
	echo "❌ Пользователь не найден!\n";
	exit(1);
}

// 🔹 Создаём токен
$tokenName = 'postman-' . date('Y-m-d-H-i-s');
$tokenResult = $user->createToken($tokenName);

// 🔹 Выводим результат (с правильной кодировкой)
echo "\n";
echo "===========================================\n";
echo "✅ API Токен создан\n";
echo "===========================================\n";
echo "Пользователь: {$user->email} (ID: {$user->id})\n";
echo "Название токена: {$tokenName}\n";
echo "\n";
echo "🔑 ТОКЕН (скопируйте, показывается 1 раз!):\n";
echo "-------------------------------------------\n";
echo $tokenResult->plainTextToken;
echo "\n-------------------------------------------\n";
echo "\n";
echo "В Postman используйте:\n";
echo "Authorization: Bearer {$tokenResult->plainTextToken}\n";
echo "\n";
