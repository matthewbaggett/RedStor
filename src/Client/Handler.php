<?php

namespace RedStor\Client;

use Closure;
use Psr\Log\LoggerInterface;
use React\Socket\ConnectionInterface;

class Handler{

    /** @var LoggerInterface */
    protected $logger;
    /** @var ConnectionInterface */
    protected $connection;
    /** @var Decoder  */
    protected $decoder;

    public function __construct(
        LoggerInterface $logger,
        ConnectionInterface $connection,
        Decoder $decoder
    )
    {
        $this->logger = $logger;
        $this->connection = $connection;
        $this->decoder = $decoder;
        $this->__attachToConnection();
    }

    protected function getClientRemoteAddress(): string
    {
        $host = parse_url($this->connection->getRemoteAddress());
        return isset($host['host']) && isset($host['port'])
            ? "{$host['host']}:{$host['port']}"
            : "UNKNOWN";
    }

    public function __attachToConnection(){
        $this->connection->on('data', Closure::fromCallable([$this, 'receiveClientMessage']));
        $this->connection->on('error', Closure::fromCallable([$this, 'handleClientException']));
        $this->connection->on('end', Closure::fromCallable([$this, 'endClient']));
        $this->connection->on('close', Closure::fromCallable([$this, 'closeClient']));

        $this->logger->info(sprintf(
            "[%s] => %s",
            $this->getClientRemoteAddress(),
            "Connected"
        ));
    }

    protected function receiveClientMessage($data)
    {
        $parsedData = $this->decoder->decode($data);

        \Kint::dump($parsedData);

        $this->logger->info(sprintf(
            "[%s] => %s",
            $this->getClientRemoteAddress(),
            implode(" ", $parsedData)
        ));
    }

    protected function endClient()
    {
        #$this->logger->info(sprintf(
        #    "[%s] == EndClient",
        #    $this->getClientRemoteAddress()
        #));
    }

    protected function closeClient()
    {
        $this->logger->info(sprintf(
            "[%s] == CloseClient",
            $this->getClientRemoteAddress()
        ));
    }

    protected function handleClientException(\Exception $e)
    {
        $this->logger->critical(sprintf(
            "[%s] ** %s",
            $this->getClientRemoteAddress(),
            $e->getMessage()
        ));
    }
}