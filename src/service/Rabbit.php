<?php
/**
 * Created by IntelliJ IDEA.
 * User: jibo
 * Date: 2018-12-05
 * Time: 20:24
 */

namespace service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rabbit
{
    private static $connection;
    private static $channel;

    static function getConnection()
    {
        if (empty(self::$connection)) {
            $f3 = \Base::instance();
            self::$connection = new AMQPStreamConnection(
                $f3->get('RMQ_HOST'),
                $f3->get('RMQ_PORT'),
                $f3->get('RMQ_USER'),
                $f3->get('RMQ_PASS')
            );
        }
        return self::$connection;
    }

    static function getChannel()
    {
        if (empty(self::$channel)) {
            self::$channel = self::getConnection()->channel();
        }
        return self::$channel;
    }

    static function send(string $exchange, string $message)
    {
        $channel = self::getChannel();
        $channel->exchange_declare($exchange, 'direct');
        $channel->basic_publish(new AMQPMessage($message), $exchange);
    }

    static function shutdown()
    {
        if (!empty(self::$channel)) {
            self::$channel->close();
        }
        if (!empty(self::$connection)) {
            self::$connection->close();
        }
    }
}
