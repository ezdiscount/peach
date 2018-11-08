<?php

namespace app;

use app\common\AppHelper;

class Login
{
    use AppHelper;

    private $administrator = ['debug'];

    function get($f3)
    {
        echo \Template::instance()->render('login.html');
    }

    function post($f3)
    {
        $logger = $f3->LOGGER;
        $username = $_POST['username'];
        $password = $_POST['password'];

        $logger->write("Receive login request: $username");

        if ($this->validate($username, $password)) {
            $logger->write("User $username login success");
            $f3->set('SESSION.AUTHENTICATION', $username);
            $f3->set('SESSION.AUTHORIZATION', in_array($username, $this->administrator) ? 'administrator' : 'user');
            echo json_encode([
                'error' => ['code' => 0]
            ]);
        } else {
            $logger->write("User $username login failure");
            echo json_encode([
                'error' => ['code' => 0, 'text' => 'login error']
            ]);
        }
    }

    function validate($username, $password)
    {
        return true;
    }
}
