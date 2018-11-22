<?php

namespace app\v1;

use db\SqlMapper;
use service\ProductRawData;

class Data
{
    private $defaultPageSize = 10;
    private $defaultCacheTime = 600;

    function pagination($f3, $args)
    {
        $pageSize = $this->defaultPageSize;
        $pageNo = intval($args['pageNo']) ?? 0;
        $affiliate = ProductRawData::getAffiliate();
        $filter = ['affiliate=? AND status=1 AND couponEnd>?', $affiliate, date('Y-m-d')];
        $mapper = new SqlMapper('product_raw');
        $pageTotal = ceil($mapper->count($filter, null, $this->defaultCacheTime) / $pageSize);
        $data = [];
        if ($pageNo >= 1 && $pageNo <= $pageTotal) {
            $offset = ($pageNo - 1) * $pageSize;
            $option = [
                'order' => 'weight DESC, create_time DESC'
            ];
            $page = $mapper->paginate($offset, $pageSize, $filter, $option, $this->defaultCacheTime);
            foreach ($page['subset'] as $item) {
                $data[] = [
                    'thumb' => $item['thumb'] . '_640x0q85s150_.webp',
                    'title' => $item['title'],
                    'reservePrice' => sprintf('%.2f', $item['price'] / 100),
                    'coupon' => sprintf('%.2f', $item['couponValue'] / 100),
                    'price' => sprintf("%.2f", ($item['price'] - $item['couponValue']) / 100),
                    'url' => $item['couponShortUrl'],
                ];
            }
        } else {
            $f3->LOGGER->write("request page $pageNo: no data");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
