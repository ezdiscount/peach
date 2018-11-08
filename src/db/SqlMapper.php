<?php

namespace db;

class SqlMapper extends SQL\Mapper
{
    function __construct($table, $ttl = 0)
    {
        parent::__construct(Mysql::instance()->get(), $table, null, $ttl);
    }
}
