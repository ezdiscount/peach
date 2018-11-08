<?php

namespace app;

class Upload extends \Web
{
    private $fileName;

    function beforeRoute($f3)
    {
        $f3->LOGGER->write($f3->VERB . ' ' . $f3->REALM);
        if ($f3->GET['name']) {
            $this->fileName = $f3->GET['name'];
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
        var_dump($receive);
    }
}
