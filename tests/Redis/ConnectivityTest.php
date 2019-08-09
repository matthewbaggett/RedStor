<?php

namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;

/**
 * @internal
 * @coversNothing
 */
class ConnectivityTest extends RedStorTest
{
    public function testConnect()
    {
        $this->assertEquals(['PONG'], $this->redis->ping());
    }
}
