<?php

namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;
use ⌬\Tests\Traits\FakeDataTrait;

/**
 * @internal
 * @coversNothing
 */
class PassThruTest extends RedStorTest
{
    use FakeDataTrait;

    private $keys = [];

    public function setUp(): void
    {
        parent::setUp();
        for ($i = 0; $i < 10; ++$i) {
            $this->keys[$i] = implode(':', $this->faker()->words(5));
        }
    }

    public function tearDown(): void
    {
        self::getDirectRedis()->del($this->keys);
        parent::tearDown();
    }

    public function testStrings()
    {
        $string = 'This is a string';
        $this->assertEquals('OK', $this->redis->set($this->keys[0], $string));
        $this->assertEquals($string, $this->redis->get($this->keys[0]));
        $this->assertEquals('This', $this->redis->getrange($this->keys[0], 0, 3));
        $this->assertEquals('ing', $this->redis->getrange($this->keys[0], -3, -1));
        $this->assertEquals($string, $this->redis->getrange($this->keys[0], 0, -1));
        $this->assertEquals('string', $this->redis->getrange($this->keys[0], 10, 100));
        $this->assertEquals($string, $this->redis->getset($this->keys[0], 'testing getset'));
        $this->assertEquals('testing getset', $this->redis->get($this->keys[0]));
        $this->assertEquals('OK', $this->redis->mset([
            $this->keys[0] => 'Apples',
            $this->keys[1] => 'Pears',
            $this->keys[2] => 'Hats',
        ]));
        $this->assertEquals(
            ['Pears', 'Hats', 'Apples'],
            $this->redis->mget([$this->keys[1], $this->keys[2], $this->keys[0]])
        );
    }

    public function testInts()
    {
        $this->assertEquals(1, $this->redis->incr($this->keys[0]));
        $this->assertEquals(0, $this->redis->decr($this->keys[0]));
        $this->assertEquals(3, $this->redis->incrby($this->keys[0], 3));
        $this->assertEquals(-2, $this->redis->decrby($this->keys[0], 5));
    }
}
