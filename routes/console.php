<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =============================================
// CRON задачи для мессенджеров
// =============================================
// Запуск получения сообщений из внешних мессенджеров раз в минуту
Schedule::command('messengers:get-messages --messenger=custom --client=1')
    ->everyMinute()
    ->name('get-messenger-messages')
    ->withoutOverlapping();
