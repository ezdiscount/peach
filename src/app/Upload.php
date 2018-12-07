<?php

namespace app;

use app\common\AppHelper;
use product\parser\ProductHC;
use product\parser\ProductNC;
use product\parser\Utils;

class Upload extends \Web
{
    use AppHelper;

    private $fileName;

    function beforeRoute(\Base $f3)
    {
        if (!$this->auth($f3)) {
            $f3->reroute($this->url('/Login'));
        } else {
            $f3->LOGGER->write($f3->VERB . ' ' . $f3->REALM);
            if ($f3->POST['name']) {
                $this->fileName = $f3->POST['name'];
            } else {
                $start = strpos($f3->URI, '/upload/');
                if ($start !== false) {
                    $start += strlen('/upload/');
                    $end = strpos($f3->URI, '?');
                    if ($end === false) {
                        $end = strlen($f3->URI);
                    }
                    $this->fileName = urldecode(substr($f3->URI, $start, $end));
                }
            }
        }
    }

    function get(\Base $f3)
    {
        if (empty($this->fileName)) {
            echo \Template::instance()->render("upload.html");
        } else {
            $file = $f3->UPLOADS . $this->fileName;
            if (is_file($file)) {
                $this->send($f3->UPLOADS . $this->fileName);
            } else {
                $f3->error(404);
            }
        }
    }

    function upload(\Base $f3)
    {
        $result = [
            'code' => -1,
            'reason' => 'undefined',
        ];
        $receive = $this->receive(null, true, false);
        if ($receive) {
            $data = Utils::load($f3->UPLOADS . $this->fileName);
            if ($data !== false) {
                if ($data[0] == ProductNC::HEADER) {
                    $result['code'] = ProductNC::parse($data);
                } else if ($data[0] == ProductHC::HEADER) {
                    $result['code'] = ProductHC::parse($data);
                } else {
                    $result['reason'] = "Unknown file header";
                }
            } else {
                $result['reason'] = "Load file error";
            }
        } else {
            $result['reason'] = "Upload file error";
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
