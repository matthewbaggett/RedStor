<?php

namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;
use ⌬\Tests\Traits\FakeDataTrait;

/**
 * @internal
 * @coversNothing
 */
class ConnectivityTest extends RedStorTest
{
    use FakeDataTrait;

    public function testConnect()
    {
        $this->assertTrue($this->redis->ping());
    }

    public function testConnectWithMessage()
    {
        $words = $this->faker()->words(3, true);
        $this->assertEquals($words, $this->redis->ping($words));
    }
}
