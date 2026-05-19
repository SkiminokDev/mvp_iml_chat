<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\ExternalApiClient;

class HomeController extends Controller
{
	protected $apiClient;

	public function __construct(ExternalApiClient $apiClient)
	{
		$this->apiClient = $apiClient;
	}

	public function index()
	{

		$apiData = $this->apiClient->getUserData();
		// Здесь могла бы быть логика выборки из БД
		$title = "Главная страница";
		$content = "Добро пожаловать на сайт!";

		// Возвращаем представление (view) и передаем данные
		return view('home', [
			'title' => 'Главная',
			'apiData' => $apiData,
		]);
	}
}
