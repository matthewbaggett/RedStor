<?php

namespace RedStor\Tests\Redis;

use RedStor\Tests\RedStorTest;
use ⌬\Tests\Traits\FakeDataTrait;
use ⌬\UUID\UUID;

/**
 * @internal
 * @coversNothing
 */
class LoginTest extends RedStorTest
{
    use FakeDataTrait;
    public const TestApp = 'Demo';
    public const Username = 'demo@redstor';
    public const Password = 'redstor';

    public function testLoginGood()
    {
        $this->assertTrue(
            $this->redis->login(self::TestApp, self::Username, self::Password)
        );
        $this->assertEquals("OK", $this->redis->set("test:" . UUID::v4(), "Try to set a key in this logged-in-state"));
    }

    public function testLoginBad_InvalidPassword()
    {
        $this->assertFalse(
            $this->redis->login(self::TestApp, self::Username, $this->faker()->password)
        );
        $this->assertEquals("-FAIL", $this->redis->set("test:" . UUID::v4(), "Try to set a key in this logged-in-state"));
    }

    public function testLoginBad_InvalidUsername()
    {
        $this->assertFalse(
            $this->redis->login(self::TestApp, $this->faker()->userName, self::Password)
        );
        $this->assertEquals("-FAIL", $this->redis->set("test:" . UUID::v4(), "Try to set a key in this logged-in-state"));

    }

    public function testLoginBad_InvalidAppName()
    {
        $this->assertFalse(
            $this->redis->login($this->faker()->company, self::Username, self::Password)
        );
        $this->assertEquals("-FAIL", $this->redis->set("test:" . UUID::v4(), "Try to set a key in this logged-in-state"));

    }
}
