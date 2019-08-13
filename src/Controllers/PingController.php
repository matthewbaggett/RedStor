<?php

namespace RedStor\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class PingController extends GatewayController
{
    use Traits\RedisClientTrait;

    /**
     * @route GET v1/ping weight=-5
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function ping(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $timeToPing = microtime(true);
        $this->redStorClient->ping();
        $timeToPing = microtime(true) - $timeToPing;

        return $response->withJson([
            'Status' => 'Okay',
            'Time' => [
                'Redis' => number_format($timeToPing, 3),
            ],
        ]);
    }
}
