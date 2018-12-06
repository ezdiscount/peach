<?php

namespace app\v1;

use app\metrics\Helper;
use db\SqlMapper;

class Data
{
    private $defaultPageSize = 10;
    private $defaultCacheTime = 0;

    function pagination($f3, $args)
    {
        $pageSize = $this->defaultPageSize;
        $pageNo = intval($args['pageNo']) ?? 0;
        $affiliate = Helper::getDomain();
        $filter = ['affiliate=? AND status=1 AND expire_date>?', $affiliate, date('Y-m-d')];
        $mapper = new SqlMapper('product');
        $pageTotal = ceil($mapper->count($filter, null, $this->defaultCacheTime) / $pageSize);
        $data = [];
        if ($pageNo >= 1 && $pageNo <= $pageTotal) {
            $option = [
                'order' => 'weight DESC, create_time DESC'
            ];
            $page = $mapper->paginate($pageNo - 1, $pageSize, $filter, $option, $this->defaultCacheTime);
            foreach ($page['subset'] as $item) {
                $data[] = [
                    'id' => $item['id'],
                    'tid' => $item['tid'],
                    'thumb' => $item['thumb'],
                    'title' => $item['title'],
                    'reservePrice' => sprintf('%.2f', $item['price'] / 100),
                    'coupon' => sprintf('%.2f', $item['coupon'] / 100),
                    'price' => sprintf("%.2f", ($item['price'] - $item['coupon']) / 100),
                    'url' => $item['url'],
                ];
            }
        } else {
            $f3->LOGGER->write("request page $pageNo: no data");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
