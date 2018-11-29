<?php

namespace app\metrics;

use Prometheus\CollectorRegistry;

class Click
{
    const CLICK_INTERVAL_INDEX = 300;
    const CLICK_INTERVAL_PRODUCT = 60;

    function index()
    {
        if (Helper::validate('click_index_' . Helper::getClientIp(), self::CLICK_INTERVAL_INDEX)) {
            CollectorRegistry::getDefault()
                ->getOrRegisterCounter(
                    '',
                    'index',
                    "counter for index",
                    [
                        'client',
                        'domain',
                    ])
                ->inc([
                    Helper::getClientIp(),
                    Helper::getDomain(),
                ]);
        }
    }

    function product($f3)
    {
        $id = $f3['PARAMS.id'];
        $ip = Helper::getClientIp();
        if (Helper::validate("click_product_${id}_${ip}", self::CLICK_INTERVAL_PRODUCT)) {
            CollectorRegistry::getDefault()
                ->getOrRegisterCounter(
                    '',
                    'product',
                    "counter for product",
                    [
                        'id',
                        'client',
                        'domain',
                    ])
                ->inc([
                    $id,
                    $ip,
                    Helper::getDomain(),
                ]);
        }
    }
}
