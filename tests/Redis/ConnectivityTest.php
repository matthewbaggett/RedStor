<?php
namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;

class ConnectivityTest extends RedStorTest
{
    public function testConnect()
    {
        $this->assertEquals(["PONG"], $this->redis->ping());
    }

}