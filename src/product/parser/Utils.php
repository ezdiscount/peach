<?php

namespace product\parser;

use PhpOffice\PhpSpreadsheet\IOFactory;

class Utils
{
    static function load($file)
    {
        $logger = new \Log(date('Y-m-d.\l\o\g'));
        $logger->write("loading file: $file");
        try {
            $spreadsheet = IOFactory::load($file);
            return $spreadsheet->getActiveSheet()->toArray();
        } catch (\Exception $e) {
            $logger->write("loading error: $file");
            ob_start();
            var_dump($e);
            $logger->write(ob_get_clean());
            return false;
        }
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
