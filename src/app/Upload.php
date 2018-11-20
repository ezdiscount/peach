<?php

namespace app;

use app\common\AppHelper;
use PhpOffice\PhpSpreadsheet\Exception;
use service\ExcelDataParser;

class Upload extends \Web
{
    use AppHelper;

    private $fileName;

    function beforeRoute($f3)
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

    function get($f3)
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

    function upload($f3)
    {
        $receive = $this->receive(null, true, false);
        if ($receive) {
            try {
                ExcelDataParser::parse($f3->UPLOADS . $this->fileName);
                echo "success";
            } catch (Exception $e) {
                var_dump($e);
            }
        } else {
            echo "failure";
        }
    }
}
