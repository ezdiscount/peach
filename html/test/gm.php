<?php
$font = 'SourceHanSansSC-Normal';
$path = __DIR__ . DIRECTORY_SEPARATOR . $font . '.otf';
if (!is_file($path)) {
    ini_set('max_execution_time', 1800);
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, 'https://github.com/adobe-fonts/source-han-sans/raw/release/OTF/SimplifiedChinese/SourceHanSansSC-Normal.otf');
    curl_setopt($cl, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($cl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
    file_put_contents($path, curl_exec($cl));
    curl_close($cl);
}
try {
    $draw = new GmagickDraw;
    $draw->annotate(100, 200, '思源黑体');
    $draw->setfont('SourceHanSansSC-Normal');
    $draw->setfontsize(100);
    $draw->setfillcolor('red');
    $gm = new Gmagick('http://qiniu.syncxplus.com/logo/testbird.png');
    header('Content-type: image/png');
    echo $gm->drawimage($draw);
} catch (Exception $e) {
    var_dump($e);
}
