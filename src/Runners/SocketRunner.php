<?php
namespace RedStor\Runners;

use Predis\Client;
use React\EventLoop\Factory as EventLoopFactory;

use RedStor\Client\Decoder;
use RedStor\Client\Encoder;
use RedStor\Client\Handler;
use RedStor\Client\Passthru;
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
    /** @var Encoder */
    protected $encoder;
    /** @var Passthru */
    protected $passthru;
    /** @var Client */
    protected $redis;

    public function __construct(
        EnvironmentService $environmentService,
        Logger $logger,
        Decoder $decoder,
        Encoder $encoder,
        Passthru $passthru,
        Client $redis
    )
    {
        $this->environmentService = $environmentService;
        $this->logger = $logger;
        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->passthru = $passthru;
        $this->redis = $redis;
        $this->loop = EventLoopFactory::create();
    }

    public function run(){
        /** @var EnvironmentService $environmentService */
        $this->logger->info("Starting socket server");
        $socket = new Socket\Server('0.0.0.0:6379', $this->loop);

        $socket->on('connection', function (Socket\ConnectionInterface $client) {
            (new Handler(
                $this->loop,
                $this->logger,
                $client,
                $this->decoder,
                $this->encoder,
                $this->passthru,
                $this->redis
            ));
        });

        $this->loop->run();
    }
}