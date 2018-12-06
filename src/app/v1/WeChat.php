<?php

namespace app\v1;


use db\SqlMapper;

class WeChat
{
    function simple($f3)
    {
        $id = $f3->get('PARAMS.id');
        $mapper = new SqlMapper('product');
        $mapper->load(['id=?', $id]);
        if ($mapper->dry()) {
            $f3->error(404, 'NO PRODUCT');
        } else {
            $f3->mset($mapper->cast());
            echo \Template::instance()->render('v1/wechat/coupon_code.html');
        }
    }
}
