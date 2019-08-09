<?php

namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;

/**
 * @internal
 * @covers \RedStor\SDK\Predis\Commands\Restart
 * @covers \RedStor\SDK\RedStorClient
 * @covers \RedStor\SDK\RedStorProfile
 */
class RestartTest extends RedStorTest
{
    public function testRestart()
    {
        $this->assertEquals(['+RESTART', 'Server is now restarting'], $this->redis->restart());
    }
}
