<?php

namespace RedStor\Tests;

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

        self::$staticRedis->flushall();
        self::$staticRedis->restart();
        sleep(2);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new RedStorClient([
            'host' => 'redstor',
        ]);
    }
}
