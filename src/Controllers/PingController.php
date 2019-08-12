<?php

namespace RedStor\Controllers;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use ⌬\Configuration\Configuration;
use ⌬\Controllers\Abstracts\HtmlController;
use ⌬\Log\Logger;
use Predis\Client as Redis;

class PingController extends HtmlController
{
    use Traits\RedisClientTrait;

    /** @var Configuration */
    private $configuration;
    /** @var Redis */
    private $redis;
    /** @var Logger */
    private $logger;
    /** @var GuzzleClient */
    private $guzzle;

    public function __construct(
        Twig $twig,
        Configuration $configuration,
        Redis $redis,
        Logger $logger
    ) {
        parent::__construct($twig);

        $this->configuration = $configuration;
        $this->redis = $redis;
        $this->logger = $logger;
        $this->redis->client('SETNAME', $this->getCalledClassStub());
    }

    /**
     * @route GET v1/ping.json
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function ping(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $timeToPing = microtime(true);
        $this->redis->ping();
        $timeToPing = microtime(true) - $timeToPing;
        return $response->withJson([
            'Status' => 'Okay',
            'Time' => [
                'Redis' => number_format($timeToPing,3),
            ]
        ]);
    }
}
