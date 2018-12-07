<?php

namespace app\v1;

use db\Mysql;
use db\SqlMapper;
use service\Rabbit;
use service\StringUtils;

class Gateway
{
    const TOKEN_CACHE_TIME = 300;
    private $logger;

    function product(\Base $f3)
    {
        $id = $f3->get('PARAMS.id');
        $token = $f3->get('REQUEST.token');
        $detect = new \Mobile_Detect;
        if ($detect->is('WeChat')) {
            $f3->reroute("/v1/wechat/simple/$id");
        } else if (isset($token) && !is_null($f3->get($token))) {
            header("location:" . $f3->get('GET.url'));
        } else {
            echo \Template::instance()->render('v1/gateway.html');
        }
    }

    function queryUser(\Base $f3)
    {
        $fid = $f3->get('POST.id');
        $user = new SqlMapper('user');
        $user->load(['fid=?', $fid]);
        if (!$user->dry()) {
            $token = $this->cacheToken($f3, $user->cast());
            echo json_encode($f3->get($token), JSON_UNESCAPED_UNICODE);
        } else {
            echo 'failed';
        }
    }

    function createUser(\Base $f3)
    {
        $db = Mysql::instance()->get();
        $db->begin();
        $fid = $f3->get('POST.id');
        $nickname = $f3->get('POST.name');
        $referral = $f3->get('POST.referral');
        $f3->clear('POST.referral');
        $mentor = new SqlMapper('user');
        $mentor->load(['affiliate=?', $referral]);
        if ($mentor->dry()) {
            echo 'invalid_referral';
        } else {
            $user = new SqlMapper('user');
            $user->load(['fid=?', $fid]);
            if ($user->dry()) {
                $user['affiliate'] = StringUtils::generateAffiliate();
                $user['nickname'] = $nickname;
                $user['facebook'] = json_encode($f3->get('POST'), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $user['update_time'] = date('Y-m-d H:i:s');
                $user->save();
                $message = json_encode([
                    'event' => 'create_user',
                    'user' => $user['id'],
                    'user_plan' => 0,
                    'mentor' => $mentor['id'],
                    'mentor_plan' => $mentor['plan'],
                ], JSON_UNESCAPED_UNICODE);
                $task = new SqlMapper('task');
                $task['name'] = 'create_user';
                $task['data'] = $message;
                $task->save();
                $this->logger->write("[event][create_user][$message]");
                Rabbit::send('peachExchange', $message);
            } else {
                $this->logger->write("user (fid: $fid) already existed");
            }
            $token = $this->cacheToken($f3, $user->cast());
            echo json_encode($f3->get($token), JSON_UNESCAPED_UNICODE);
        }
        $db->commit();
    }

    function cacheToken(\Base $f3, array $user)
    {
        $token = StringUtils::generateToken();
        $f3->set($token, array_merge(['token' => $token], $user), self::TOKEN_CACHE_TIME);
        return $token;
    }

    function beforeRoute(\Base $f3)
    {
        $this->logger = $f3->get('LOGGER');
    }
}
