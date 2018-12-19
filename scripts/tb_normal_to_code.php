<?php

$query = http_build_query([
    'text' => '飞利浦 mini dp 转hdmi/vga转换器mac 雷电接口迷你dp苹果电脑macbook air微软surface pro外接投影仪电视4K',
    'pic' => 'https://img.alicdn.com/imgextra/i4/3681142570/TB2bGNLq9BYBeNjy0FeXXbnmFXa_!!3681142570.jpg',
    'url' => 'https://item.taobao.com/item.htm?id=564577488595',
    'vekey' => getenv('VE_KEY'),
]);

$apiUrl = 'http://api.vephp.com/tbtkl?' . $query;

echo $apiUrl, PHP_EOL;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_exec($ch);
