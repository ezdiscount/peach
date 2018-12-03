<?php

namespace app\v1;

use db\SqlMapper;
use service\StringUtils;

class Gateway
{
    const TOKEN_CACHE_TIME = 300;
    private $logger;
    private $user;

    function product($f3)
    {
        $id = $f3->get('PARAMS.id');
        $detect = new \Mobile_Detect;
        if ($detect->is('WeChat')) {
            $f3->reroute("/v1/wechat/simple/$id");
        } else if ($this->auth($f3)) {
            header("location:" . $f3->get('GET.url'));
        } else {
            echo \Template::instance()->render('v1/gateway.html');
        }
    }

    function auth($f3)
    {
        $token = $_REQUEST['token'] ?? false;
        $this->logger->write("user token: $token");
        if ($token !== false) {
            $this->user = $f3->get($token);
            if (!is_null($this->user)) {
                ob_start();
                var_dump($this->user);
                $this->logger->write(ob_get_clean());
                return true;
            }
        }
        $this->logger->write("user auth failed");
        return false;
    }

    function queryUser($f3)
    {
        $fid = $f3->POST['id'];
        $user = new SqlMapper('user');
        $user->load(['fid=?', $fid]);
        if (!$user->dry()) {
            $token = $this->setToken($f3, $user);
            echo json_encode($f3->get($token), JSON_UNESCAPED_UNICODE);
        } else {
            echo 'failed';
        }
    }

    function createUser($f3)
    {
        $fid = $f3->POST['id'];
        $nickname = $f3->POST['name'];
        $referral = $f3->POST['referral'];
        unset($f3->POST['referral']);
        $user = new SqlMapper('user');
        $user->load(['fid=?', $fid]);
        if ($user->dry()) {
            $user['affiliate'] = StringUtils::generateAffiliate();
            $user['referral'] = $referral;
            $user['nickname'] = $nickname;
            $user['facebook'] = json_encode($f3->POST, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $user['update_time'] = date('Y-m-d H:i:s');
            $user->save();
            // TODO: trigger user plan check
            $token = $this->setToken($f3, $user);
            echo json_encode($f3->get($token), JSON_UNESCAPED_UNICODE);
        } else {
            $this->logger->write("user (fid: $fid) already existed");
            echo 'failed';
        }
    }

    function validateReferral($f3)
    {
        $referral = $f3->POST['referral'];
        $user = new SqlMapper('user');
        $user->load(['affiliate=?', $referral]);
        if ($user->dry()) {
            echo 'failed';
        } else {
            echo 'success';
        }
    }

    function setToken(\Base $f3, SqlMapper $user)
    {
        $token = StringUtils::generateToken();
        $f3->set($token, array_merge(['token' => $token], $user->cast()), self::TOKEN_CACHE_TIME);
        return $token;
    }

    function beforeRoute($f3)
    {
        $this->logger = $f3->LOGGER;
    }
}
