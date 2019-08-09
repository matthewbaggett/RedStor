<?php

namespace RedStor\Client;

use Closure;
use Predis\Client;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use RedStor\Exceptions\RESPDecodeException;
use ⌬\Log\Logger;

class Handler{

    /** @var LoopInterface */
    protected $loop;
    /** @var LoggerInterface */
    protected $logger;
    /** @var ConnectionInterface */
    protected $connection;
    /** @var Decoder  */
    protected $decoder;
    /** @var Encoder */
    protected $encoder;
    /** @var Passthru */
    protected $passthru;
    /** @var Client */
    protected $redis;

    public function __construct(
        LoopInterface $loop,
        Logger $logger,
        ConnectionInterface $connection,
        Decoder $decoder,
        Encoder $encoder,
        Passthru $passthru,
        Client $redis
    )
    {
        $this->loop = $loop;
        $this->logger = $logger;
        $this->connection = $connection;
        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->passthru = $passthru;
        $this->redis = $redis;
        $this->__attachToConnection();
    }

    public function __attachToConnection()
    {
        $this->connection->on('data',  Closure::fromCallable([$this, 'receiveClientMessage']));
        $this->connection->on('error', Closure::fromCallable([$this, 'handleClientException']));
        $this->connection->on('end',   Closure::fromCallable([$this, 'endClient']));
        $this->connection->on('close', Closure::fromCallable([$this, 'closeClient']));
    }

    protected function receiveClientMessage($data)
    {
        $debugData = str_replace("\n","\\n", $data);
        $debugData = str_replace("\r","\\r", $debugData);

        #\Kint::dump($data);
        if(trim($data) == 'PING'){
            #$data = "*1\r\n$4\r\nPING\r\n";
            $this->encoder->sendInline($this->connection, "+PING");
            return;
        }

        $parsedData = $this->decoder->decode($data);

        $this->logger->info(sprintf(
            "[%s] => %s (%s)",
            $this->connection->getRemoteAddress(),
            implode(" ", $parsedData),
            trim($debugData),
        ));

        #\Kint::dump($parsedData[0]);

        switch($parsedData[0]){
            case 'RESTART':
                $this->encoder->writeStrings($this->connection, ["+RESTART", "Server is now restarting"]);
                $this->connection->end();
                $this->loop->addTimer(1.0, function(){
                    die("Restarting!\n");
                });
                break;
            case 'PING':
                $this->encoder->sendPong($this->connection, isset($parsedData[1]) ? $parsedData[1] : null);
                break;
            case in_array($parsedData[0], $this->passthru->getPassthruCommands()):
                $this->passthru->passthru($this->connection, $parsedData);
                break;
            default:
                $this->encoder->sendError($this->connection, sprintf("Sorry, %s is not a valid command.", $parsedData[0]));
        }

    }

    protected function endClient() {}

    protected function closeClient() {}

    protected function handleClientException(\Exception $e) {}
}