<?php
namespace test\db;

use db\Mysql;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    public function testConnection()
    {
        $db = Mysql::instance()->get();
        $this->assertNotEmpty($db);
        $this->assertNotEmpty($db->exec('SELECT 1'));
    }
}
