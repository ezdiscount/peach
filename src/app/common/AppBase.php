<?php

namespace app\common;

class AppBase
{
    use AppHelper;

    /**
     * @var array
     * @link https://github.com/bcosca/fatfree#about-the-f3-error-handler
     */
    protected $error = [
        'code' => -1,    //HTTP status code or error code
        'status' => '',  //a brief description of code
        'text' => '',    //a brief description of error
        'trace' => ''    //stack trace of detail information
    ];

    /**
     * @var array: [name, role]
     */
    protected $user;

    function beforeRoute($f3)
    {
        if (!$this->auth($f3)) {
            $f3->reroute($this->url('/Login'));
        }
    }

    function jsonResponse($data = [])
    {
        $result = [
            'error' => $this->error,
            'data' => $data
        ];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
