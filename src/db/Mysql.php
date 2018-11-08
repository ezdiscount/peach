<?php

namespace db;

class Mysql extends \Prefab
{
    private $db;

    function get()
    {
        return $this->db;
    }

    function __construct()
    {
        $f3 = \Base::instance();
        $this->db = new SQL(
            $f3->get('MYSQL_DSN'),
            $f3->get('MYSQL_USER'),
            $f3->get('MYSQL_PASSWORD')
        );
    }
}
