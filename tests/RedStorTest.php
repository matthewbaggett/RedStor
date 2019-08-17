<?php

namespace RedStor\Tests;

use Predis\Client as PredisClient;
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

    public static function resetRedis(RedStorClient $redis): void
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
        self::assertDemoAccount($redis);
    }

    public static function getDirectRedis(): PredisClient
    {
        $env = array_merge($_ENV, $_SERVER);
        ksort($env);
        // Since we can't actually call hset or set without already been auth'd,
        // and to be authed we need to call these functions,
        // lets dance around and talk directly to redis for a moment
        $directRedis = new PredisClient($env['REDIS_HOST']);

        return $directRedis;
    }

    public static function assertDemoAccount(RedStorClient $redis): void
    {
        $directRedis = self::getDirectRedis();
        $directRedis->hset(sprintf(RedStor::KEY_AUTH_APP, RedStorTest::DEMO_APP), RedStorTest::DEMO_USERNAME, password_hash(RedStorTest::DEMO_PASSWORD, PASSWORD_DEFAULT));
        $directRedis->set(sprintf(RedStor::KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR, RedStorTest::DEMO_APP), 10000);
        $directRedis->set(sprintf(RedStor::KEY_LIMIT_RATELIMIT_REQUESTSPERHOUR_AVAILABLE, RedStorTest::DEMO_APP), 10000);
    }

    protected static function login(RedStorClient $redis, string $app = self::DEMO_APP, string $username = self::DEMO_USERNAME, string $password = self::DEMO_PASSWORD): void
    {
        $loginSuccess = $redis->login($app, $username, $password);

        //printf(
        //    'Logging in as %s (%s)... %s'.PHP_EOL,
        //    $app,
        //    $username,
        //    $loginSuccess ? 'Successful' : 'Failure'
        //);

        if (!$loginSuccess) {
            throw new \Exception("Login did not succeed with details {$app}/{$username} ({$password})");
        }
    }
}
