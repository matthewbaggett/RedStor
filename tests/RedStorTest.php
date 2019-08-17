<?php

namespace RedStor\Tests;

use Predis\Connection\ConnectionException;
use RedStor\RedStor;
use RedStor\SDK\RedStorClient;
use ⌬\Tests\TestCase;

abstract class RedStorTest extends TestCase
{
    public const DEMO_APP = 'Demo';
    public const DEMO_USERNAME = 'demo@redstor';
    public const DEMO_PASSWORD = 'redstor';
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

        self::login(self::$staticRedis);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new RedStorClient([
            'host' => 'redstor',
        ]);
        self::login($this->redis);
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
                usleep(1000);
            }
        }
        $redis->getPredis()->hset(sprintf(RedStor::KEY_AUTH_APP, RedStorTest::DEMO_APP), RedStorTest::DEMO_USERNAME, password_hash(RedStorTest::DEMO_PASSWORD, PASSWORD_DEFAULT));
        $redis->getPredis()->set(sprintf(RedStor::KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR, RedStorTest::DEMO_APP), 10000);
        $redis->getPredis()->set(sprintf(RedStor::KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR_AVAILABLE, RedStorTest::DEMO_APP), 10000);
    }

    protected static function login(RedStorClient $redis, string $app = self::DEMO_APP, string $username = self::DEMO_USERNAME, string $password = self::DEMO_PASSWORD)
    {
        $loginSuccess = $redis->login($app, $username, $password);
        printf(
            'Logging in as %s/%s... %s',
            $app,
            $username,
            $loginSuccess ? 'Successful' : 'Failure'
        );

        if (!$loginSuccess) {
            throw new \Exception("Login did not succeed with details {$app}/{$username} ({$password})");
        }
    }
}
