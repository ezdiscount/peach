<?php

$query = http_build_query([
    'para' => '520177423084',
    'vekey' => getenv('VE_KEY'),
]);

$apiUrl = 'http://api.vephp.com/hcapi?' . $query;

echo $apiUrl, PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_exec($ch);
