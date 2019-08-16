<?php

namespace RedStor\Tests;

use Predis\Connection\ConnectionException;
use RedStor\SDK\RedStorClient;
use ⌬\Tests\TestCase;

abstract class RedStorTest extends TestCase
{
    /** @var RedStorClient */
    protected static $staticRedis;
    /** @var RedStorClient */
    protected $redis;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$staticRedis = new RedStorClient([
            'host' => 'redstor',
        ]);
        self::resetRedis(self::$staticRedis);

        self::$staticRedis->login('Demo', 'demo@redstor', 'redstor');
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new RedStorClient([
            'host' => 'redstor',
        ]);
        $this->redis->login('Demo', 'demo@redstor', 'redstor');
    }

    public static function resetRedis(RedStorClient $redis)
    {
        $redis->flushall();
        $redis->restart();
        sleep(1);
        $connected = false;
        while (false == $connected) {
            try {
                $redis->ping();
                $connected = true;
            } catch (ConnectionException $conex) {
            }
        }
        $redis->hset('RedStor:Auth:Demo', 'demo@redstor', '$2y$10$VHoTQjWEBDQgc6n01h.VFOv9DiigXpav8rMCVWV9ARsHTqQ3zYro2');
        $redis->set('RedStor:RateLimit:Demo:RequestsPerHour', 10000);
        $redis->set('RedStor:RateLimit:Demo:RequestsPerHourAvailable', 10000);
    }
}
