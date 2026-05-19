<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ExternalApiClient
{
	protected string $baseUrl;
	protected string $token;

	public function __construct()
	{
		$this->baseUrl = rtrim(config('external_api.url'), '/');
		$this->token = config('external_api.token');
	}

	public function getUserData():string
	{
		return 'это тест api';
	}
}