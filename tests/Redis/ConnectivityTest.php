<?php

namespace RedStor\Tests\Redis;

use Predis\Response\Status;
use RedStor\Tests\RedStorTest;

/**
 * @internal
 * @coversNothing
 */
class ConnectivityTest extends RedStorTest
{
    public function testConnect()
    {
        /** @var Status $pong */
        $pong = $this->redis->ping();
        $this->assertInstanceOf(Status::class, $pong);
        $this->assertEquals('OK', $pong->getPayload());
    }
}
