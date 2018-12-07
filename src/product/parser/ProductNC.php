<?php

namespace product\parser;

use app\metrics\Helper;
use db\Mysql;
use db\SqlMapper;

/**
 * Class ProductNC
 * @package product\parser
 *
 * Process product with normal commission rate
 *
 */
class ProductNC
{
    const HEADER = [
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

    const HEADER_MAP = [
        '0' => 'tid',
        '1' => 'title',
        '2' => 'thumb',
        '5' => 'price',
        '7' => 'commissionRate',
        '8' => 'commissionValue',
        '10' => 'tkShortUrl',
        '12' => 'tkCode',
        '15' => 'couponValue',
        '17' => 'couponEnd',
        '19' => 'couponCode',
        '20' => 'couponShortUrl',
    ];

    static function parse(array $raw) : int
    {
        unset($raw[0]);
        $db = Mysql::instance()->get();
        $db->begin();
        $header = array_flip(self::HEADER_MAP);
        $mapper = new SqlMapper('product');
        foreach ($raw as $row) {
            $tid = $row[$header['tid']];
            if (!$tid) {
                continue;
            }
            $affiliate = Helper::getDomain();
            $mapper->load(['affiliate=? AND tid=?', $affiliate, $tid]);
            $mapper['affiliate'] = $affiliate;
            $mapper['status'] = 1;
            $mapper['create_time'] = date('Y-m-d H:i:s');
            $price = Utils::calcPriceWithCoupon($row[$header['price']], $row[$header['couponValue']]);
            $mapper['tid'] = $tid;
            $mapper['title'] = $row[$header['title']] ?? '';
            $mapper['thumb'] = str_replace(['http:', 'https:'], '', $row[$header['thumb']]) ?? '';
            $mapper['url'] = $row[$header['couponShortUrl']] ?? $row[$header['tkShortUrl']];
            $mapper['code'] = $row[$header['couponCode']] ?? $row[$header['tkCode']];
            $mapper['price'] = intval($price[0] * 100);
            $mapper['coupon'] = intval($price[1] * 100);
            $mapper['expire_date'] = $mapper['coupon'] ? Utils::checkDate($row[$header['couponEnd']]) : date('Y-m-d', strtotime('+ 30 days'));
            $mapper['commission_rate'] = $row[$header['commissionRate']] ?? 0;
            $mapper['commission_value'] = intval($row[$header['commissionValue']] * 100);
            $mapper->save();
        }
        $db->commit();
        return 0;
    }
}
