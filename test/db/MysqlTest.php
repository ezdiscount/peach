<?php
namespace test\db;

use db\Mysql;
use db\SqlMapper;
use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    private $username = 'debug';
    private $password = '123456';

    public function testConnection()
    {
        $db = Mysql::instance()->get();
        $this->assertNotEmpty($db);
        $this->assertNotEmpty($db->exec('SELECT 1'));
    }

    public function testAdmin()
    {
        $admin = new SqlMapper('admin');
        $admin->load(['username=?', $this->username]);
        if ($admin->dry()) {
            $admin['username'] = $this->username;
            $admin['password'] = password_hash(md5($this->password), PASSWORD_BCRYPT);
            $admin['status'] = 1;
            $admin->save();
        } else {
            var_dump($admin->cast());
        }
        $this->assertTrue(password_verify(md5($this->password), $admin['password']));
    }
}
