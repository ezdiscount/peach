<?php
/**
 * Created by IntelliJ IDEA.
 * User: jibo
 * Date: 2018-12-03
 * Time: 15:04
 */

namespace service;


use db\SqlMapper;
use Ramsey\Uuid\Uuid;

class StringUtils
{
    const AFFILIATE_LENGTH = 6;
    const TOKEN_LENGTH = 8;

    static function generateAffiliate()
    {
        $user = new SqlMapper('user');
        while (true) {
            $uuid = self::uuid();
            if (is_numeric($uuid[0])) {
                $affiliate = 'r' . substr($uuid, 0, self::AFFILIATE_LENGTH - 1);
            } else {
                $affiliate = substr($uuid, 0, self::AFFILIATE_LENGTH);
            }
            $user->load(['affiliate=?', $affiliate]);
            if ($user->dry()) {
                return $affiliate;
            }
        }
    }

    static function generateToken()
    {
        $f3 = \Base::instance();
        while (true) {
            $uuid = self::uuid();
            if (is_numeric($uuid[0])) {
                $token = 'r' . substr($uuid, 0, self::TOKEN_LENGTH - 1);
            } else {
                $token = substr($uuid, 0, self::TOKEN_LENGTH);
            }
            if (!$f3->get($token)) {
                return $token;
            }
        }
    }

    static function uuid() : string
    {
        try {
            return Uuid::uuid1()->toString();
        } catch (\Exception $e) {
        }
        return '';
    }
}
