<?php
// memory_test.php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Memory limit: " . ini_get('memory_limit') . "\n";
echo "Memory usage: " . memory_get_usage() . "\n";
echo "Peak memory: " . memory_get_peak_usage() . "\n";

$request = Illuminate\Http\Request::capture();
$request->headers->set('Authorization', 'Bearer ' . $argv[1]);

try {
	$response = $kernel->handle($request);
	echo "Response status: " . $response->status() . "\n";
	echo "Memory after response: " . memory_get_usage() . "\n";
} catch (Exception $e) {
	echo "Error: " . $e->getMessage() . "\n";
}