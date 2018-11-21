<?php

namespace service;

use Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class ProductRawData
{
    const RAW = RUNTIME . '/raw.json';
    const HEADER = [
        '0' => 'tid',
        '1' => 'info',
        '2' => 'thumb',
        '3' => 'detailUrl',
        '4' => 'store',
        '5' => 'price',
        '6' => 'salesVolume',
        '7' => 'commissionRate',
        '8' => 'commissionValue',
        '9' => 'sellerWaWa',
        '10' => 'tkShortUrl',
        '11' => 'tkUrl',
        '12' => 'tkCode',
        '13' => 'couponTotal',
        '14' => 'couponAvailable',
        '15' => 'couponValue',
        '16' => 'couponStart',
        '17' => 'couponEnd',
        '18' => 'couponUrl',
        '19' => 'couponCode',
        '20' => 'couponShortUrl',
        '21' => 'isRecommend',
        '22' => 'groupThresh',
        '23' => 'groupPrice',
        '24' => 'groupCommission',
        '25' => 'groupStart',
        '26' => 'groupEnd',
    ];
    const RAW_HEADER = [
        '0' => '商品id',
        '1' => '商品名称',
        '2' => '商品主图',
        '3' => '商品详情页链接地址',
        '4' => '店铺名称',
        '5' => '商品价格(单位：元)',
        '6' => '商品月销量',
        '7' => '收入比率(%)',
        '8' => '佣金',
        '9' => '卖家旺旺',
        '10' => '淘宝客短链接(300天内有效)',
        '11' => '淘宝客链接',
        '12' => '淘口令(30天内有效)',
        '13' => '优惠券总量',
        '14' => '优惠券剩余量',
        '15' => '优惠券面额',
        '16' => '优惠券开始时间',
        '17' => '优惠券结束时间',
        '18' => '优惠券链接',
        '19' => '优惠券淘口令(30天内有效)',
        '20' => '优惠券短链接(300天内有效)',
        '21' => '是否为营销计划商品',
        '22' => '成团人数',
        '23' => '成团价',
        '24' => '成团佣金',
        '25' => '拼团开始时间',
        '26' => '拼团结束时间',
    ];

    /**
     * @param $file : absolute path of excel
     * @see html/img/data_format.png
     * @return bool
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    static function parse($file)
    {
        $logger = Log::instance(date('Y-m-d.\l\o\g'));
        $logger->write("parse: $file");
        $suffix = pathinfo($file, PATHINFO_EXTENSION);
        if ($suffix === 'xls') {
            $reader = new Xls();
            $spreadsheet = $reader->load($file);
        } else if ($suffix === 'xlsx') {
            $spreadsheet = IOFactory::load($file);
        } else {
            $logger->write("Unsupported file type: $suffix");
            return false;
        }
        $raw = $spreadsheet->getActiveSheet()->toArray();
        if (self::RAW_HEADER === $raw[0]) {
            unset($raw[0]);
            self::raw2File($raw);
        } else {
            ob_start();
            var_dump($raw[0]);
            $logger->write('Unsupported file header:');
            $logger->write(ob_get_clean());
        }
        return true;
    }

    static function raw2File($raw)
    {
        $content = '[';
        $header = array_flip(self::HEADER);
        foreach ($raw as $row) {
            $price = self::calcPriceWithCoupon($row[$header['price']], $row[$header['couponValue']]);
            $item = [
                'thumb' => $row[$header['thumb']] . '_640x0q85s150_.webp',
                'title' => $row[$header['info']],
                'reservePrice' => $price[0],
                'coupon' => $price[1],
                'price' => sprintf("%.2f", $price[2]),
                'url' => $row[$header['couponShortUrl']],
            ];
            $content .= json_encode($item, JSON_UNESCAPED_UNICODE);
            $content .= ',';
        }
        $i = strrpos($content, ',');
        if ($i === false) {
            $content .= ']';
        } else {
            $content = substr($content, 0, $i) . ']';
        }
        file_put_contents(self::RAW, $content,LOCK_EX);
    }

    /**
     * @param $price
     * @param $couponValue
     * @return array [price, coupon, price - coupon]
     */
    static function calcPriceWithCoupon($price, $couponValue)
    {
        $match = [];
        $pattern = '/\d+/';
        preg_match_all($pattern, $couponValue,$match);
        $count = $match ? count($match[0]) : 0;
        if ($count == 2) {
            $coupon = $match[0];
            return ($price >= intval($coupon[0])) ? [$price, $coupon[1], max(0, $price - intval($coupon[1]))] : [$price, $coupon[1], $price];
        } else if ($count == 1) {
            $coupon = $match[0];
            return [$price, $coupon[0], max(0, $price - intval($coupon[0]))];
        } else {
            return [$price, 0, $price];
        }
    }
}
