<?php
// curl.php
$ch = curl_init('http://127.0.0.1:8000/api/user');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	'Authorization: Bearer ' . $argv[1],
	'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$error = curl_error($ch);

echo "Response: $response\n";
echo "Info: " . print_r($info, true) . "\n";
echo "Error: $error\n";
