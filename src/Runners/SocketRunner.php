<?php
namespace RedStor\Runners;

use React\EventLoop\Factory as EventLoopFactory;

use RedStor\Client\Decoder;
use RedStor\Client\Handler;
use ⌬\Services\EnvironmentService;
use ⌬\Log\Logger;
use React\Socket;

class SocketRunner{
    /** @var EnvironmentService */
    protected $environmentService;
    /** @var Logger */
    protected $logger;
    /** @var \React\EventLoop\LoopInterface */
    protected $loop;
    /** @var Decoder */
    protected $decoder;

    public function __construct(
        EnvironmentService $environmentService,
        Logger $logger,
        Decoder $decoder
    )
    {
        $this->environmentService = $environmentService;
        $this->logger = $logger;
        $this->decoder = $decoder;
        $this->loop = EventLoopFactory::create();
    }

    public function run(){
        /** @var EnvironmentService $environmentService */
        $this->logger->info("Starting socket server");
        $socket = new Socket\Server('0.0.0.0:6379', $this->loop);
        echo "Running...\n";

        $scope = $this;

        $socket->on('connection', function (Socket\ConnectionInterface $client) use ($scope) {
            (new Handler($scope->logger, $client, $this->decoder));
        });

        $this->loop->run();
    }
}