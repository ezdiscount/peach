<?php

namespace product\parser;

use app\metrics\Helper;
use db\Mysql;
use db\SqlMapper;

/**
 * Class ProductHC
 * @package product\parser
 *
 * Process product with high commission rate
 *
 */
class ProductHC
{
    const HEADER = [
        '0' => '商品id',
        '1' => '商品名称',
        '2' => '商品主图',
        '3' => '商品详情页链接地址',
        '4' => '店铺名称',
        '5' => '商品价格(单位：元)',
        '6' => '商品月销量',
        '7' => '通用收入比率(%)',
        '8' => '通用佣金',
        '9' => '活动状态',
        '10' => '活动收入比率(%)',
        '11' => '活动佣金',
        '12' => '活动开始时间',
        '13' => '活动结束时间',
        '14' => '卖家旺旺',
        '15' => '淘宝客短链接(300天内有效)',
        '16' => '淘宝客链接',
        '17' => '淘口令(30天内有效)',
        '18' => '优惠券总量',
        '19' => '优惠券剩余量',
        '20' => '优惠券面额',
        '21' => '优惠券开始时间',
        '22' => '优惠券结束时间',
        '23' => '优惠券链接',
        '24' => '优惠券淘口令(30天内有效)',
        '25' => '优惠券短链接(300天内有效)',
    ];

    const HEADER_MAP = [
        '0' => 'tid',
        '1' => 'title',
        '2' => 'thumb',
        '5' => 'price',
        '7' => 'commissionRate',
        '8' => 'commissionValue',
        // high commission start
        '9' => 'isHC',
        '10' => 'hcRate',
        '11' => 'hcCommission',
        '13' => 'hcEnd',
        // high commission end
        '15' => 'tkShortUrl',
        '17' => 'tkCode',
        '20' => 'couponValue',
        '22' => 'couponEnd',
        '24' => 'couponCode',
        '25' => 'couponShortUrl',
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
            $mapper['expire_date'] = Utils::checkDate($row[$header['hcEnd']]);
            $mapper['commission_rate'] = $row[$header['hcRate']] ?? 0;
            $mapper['commission_value'] = intval($row[$header['hcCommission']] * 100);
            $mapper->save();
        }
        $db->commit();
        return 0;
    }
}
