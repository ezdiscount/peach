<?php

die("out of date\n");

use db\SqlMapper;

require_once __DIR__ . '/index.php';

function logging($log)
{
    if (is_scalar($log)) {
        echo $log, PHP_EOL;
        $logger = new Log(date('Y-m-d.\l\o\g'));
        $logger->write($log);
    } else {
        print_r($log);
    }
}


$old = new SqlMapper('product_raw');
$new = new SqlMapper('product');

$old->load();

while (!$old->dry()) {
    $data =[
        'affiliate' => $old['affiliate'],
        'hc' => $old['hc'],
        'tid' => $old['tid'],
        'title' => $old['title'],
        'thumb' => $old['thumb'],
        'url' => $old['couponShortUrl'],
        'code' => $old['couponCode'],
        'price' => $old['price'],
        'coupon' => $old['couponValue'],
        'expire_date' => date('Y-m-d', strtotime($old['couponEnd'])),
        'commission_rate' => $old['commissionRate'],
        'commission_value' => $old['commissionValue'],
    ];
    $old->next();
    $new->reset();
    $new->copyfrom($data);
    $new->save();
}
