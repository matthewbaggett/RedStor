<?php
namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;

class CreateModelTest extends RedStorTest
{
    public function testConnect()
    {
        $this->assertEquals(
            ["PONG"], $this->redis->ping());
    }
}