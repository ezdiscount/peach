<?php
// Usage: php tb_pull_order.php '2018-12-19 10:55:00'

$query = http_build_query([
    'span' => 1200,
    'start_time' => $argv[1],
    'vekey' => getenv('VE_KEY'),
]);
$apiUrl = 'http://apiorder.vephp.com/order?' . $query;

echo $apiUrl, PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_exec($ch);
