<?php

namespace service;

use app\metrics\Helper;
use db\Mysql;
use db\SqlMapper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class ProductRawDataHC
{
    const HEADER = [
        '0' => 'tid',
        '1' => 'title',
        '2' => 'thumb',
        '3' => 'detailUrl',
        '4' => 'store',
        '5' => 'price',
        '6' => 'salesVolume',
        '7' => 'commissionRate',
        '8' => 'commissionValue',
        // high commission start
        '9' => 'isHC',
        '10' => 'hcRate',
        '11' => 'hcCommission',
        '12' => 'hcStart',
        '13' => 'hcEnd',
        // high commission end
        '14' => 'sellerWaWa',
        '15' => 'tkShortUrl',
        '16' => 'tkUrl',
        '17' => 'tkCode',
        '18' => 'couponTotal',
        '19' => 'couponAvailable',
        '20' => 'couponValue',
        '21' => 'couponStart',
        '22' => 'couponEnd',
        '23' => 'couponUrl',
        '24' => 'couponCode',
        '25' => 'couponShortUrl',
    ];
    const RAW_HEADER = [
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
    static $affiliate;

    /**
     * @param $file : absolute path of excel
     * @see html/img/data_format.png
     * @return array [code, reason]
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    static function parse($file)
    {
        $logger = \Base::instance()->LOGGER;
        $logger->write("parse: $file");
        $suffix = pathinfo($file, PATHINFO_EXTENSION);
        if ($suffix === 'xls') {
            $reader = new Xls();
            $spreadsheet = $reader->load($file);
        } else if ($suffix === 'xlsx') {
            $spreadsheet = IOFactory::load($file);
        } else {
            $logger->write("Unsupported file type: $suffix");
            return [
                'code' => -1,
                'reason' => "Unsupported file type: $suffix"
            ];
        }
        $raw = $spreadsheet->getActiveSheet()->toArray();
        if (self::RAW_HEADER === $raw[0]) {
            unset($raw[0]);
            self::raw2Database($raw);
        } else {
            ob_start();
            var_dump($raw[0]);
            $logger->write('Unsupported file header:');
            $logger->write(ob_get_clean());
            return [
                'code' => -1,
                'reason' => "Unsupported file header"
            ];
        }
        return ['code' => 0];
    }

    static function raw2Database($raw)
    {
        $db = Mysql::instance()->get();
        $db->begin();
        $header = array_flip(self::HEADER);
        $mapper = new SqlMapper('product_raw');
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
            $mapper['hc'] = 1;
            $price = self::calcPriceWithCoupon($row[$header['price']], $row[$header['couponValue']]);
            $mapper['tid'] = $tid;
            $mapper['title'] = $row[$header['title']] ?? '';
            $mapper['thumb'] = str_replace(['http:', 'https:'], '', $row[$header['thumb']]) ?? '';
            $mapper['detailUrl'] = str_replace(['http:', 'https:'], '', $row[$header['detailUrl']]) ?? '';
            $mapper['store'] = $row[$header['store']] ?? '';
            $mapper['price'] = intval($price[0] * 100);
            $mapper['salesVolume'] = $row[$header['salesVolume']] ?? 0;
            $mapper['commissionRate'] = $row[$header['commissionRate']] ? str_replace('%', '', $row[$header['commissionRate']]) : 0;
            $mapper['commissionValue'] = intval($row[$header['commissionValue']] * 100);
            $mapper['sellerWaWa'] = $row[$header['sellerWaWa']] ?? '';
            $mapper['tkShortUrl'] = $row[$header['tkShortUrl']] ?? '';
            $mapper['tkUrl'] = $row[$header['tkUrl']] ?? '';
            $mapper['tkCode'] = $row[$header['tkCode']] ?? '';
            $mapper['couponTotal'] = intval($row[$header['couponTotal']]);
            $mapper['couponAvailable'] = intval($row[$header['couponAvailable']]);
            $mapper['couponValue'] = intval($price[1] * 100);
            $mapper['couponStart'] = self::getStartDate($row[$header['couponStart']], $row[$header['hcStart']]);
            $mapper['couponEnd'] = self::getEndDate($row[$header['hcEnd']], $row[$header['couponEnd']]);
            $mapper['couponUrl'] = $row[$header['couponUrl']] ?? '';
            $mapper['couponCode'] = $row[$header['couponCode']] ?? '';
            $mapper['couponShortUrl'] = $row[$header['couponShortUrl']] ?? '';
            $mapper->save();
            $mapper->reset();
        }
        $db->commit();
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

    static function getStartDate($hc, $coupon)
    {
        $hc = self::checkDate($hc);
        $coupon = self::checkDate($coupon);
        return (strtotime($hc) > strtotime($coupon)) ? $hc : $coupon;
    }

    static function getEndDate($hc, $coupon)
    {
        $hc = self::checkDate($hc);
        $coupon = self::checkDate($coupon);
        return (strtotime($hc) < strtotime($coupon)) ? $hc : $coupon;
    }

    static function checkDate($date)
    {
        $default = '2018-01-01';
        if ($date && (strtotime($date) > strtotime($default))) {
            return $date;
        } else {
            return $default;
        }
    }
}
