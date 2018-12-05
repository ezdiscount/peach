<?php
/**
 * Created by IntelliJ IDEA.
 * User: jibo
 * Date: 2018-12-05
 * Time: 21:16
 */

namespace test\rabbit;

use PHPUnit\Framework\TestCase;
use service\Rabbit;

class RabbitSendTest extends TestCase
{
    function test()
    {
        Rabbit::send('peachExchange', json_encode([
            'message' => 'Hello, world!'
        ]));
        Rabbit::shutdown();
    }
}
