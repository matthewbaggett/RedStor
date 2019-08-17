<?php

namespace RedStor\Client;

use Predis\Connection\ConnectionException;
use Predis\Profile\RedisVersion320;
use Predis\Response\ServerException;
use Predis\Response\Status;
use React\Socket\ConnectionInterface;
use RedStor\SDK\RedStorClient as Client;
use ⌬\Log\Logger;

class Passthru
{
    public const REDIS_SEPERATOR = "\r\n";

    /** @var Client */
    protected $client;

    /** @var Logger */
    protected $logger;

    /** @var Encoder */
    protected $encoder;

    public function __construct(
        Client $client,
        Logger $logger,
        Encoder $encoder
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    public function getPassthruCommands(): array
    {
        $version = new RedisVersion320();

        return array_keys($version->getSupportedCommands());
    }

    public function resetConnection() : void
    {
        echo "RESETTING CONNECTION\n";
        $this->client->disconnect();
        $this->client->connect();
    }

    public function passthru(ConnectionInterface $connection, $passthruCommand)
    {
        $this->resetConnection();
        //\Kint::dump($passthruCommand);
        $command = array_shift($passthruCommand);
        #\Kint::dump($command, $passthruCommand);

        try {
            $serverResponse = $this->client->__call($command, $passthruCommand);
            \Kint::dump($serverResponse);
            if ($serverResponse instanceof Status) {
                $connection->write('+OK'.self::REDIS_SEPERATOR);
            } else {
                if (is_numeric($serverResponse)) {
                    $this->encoder->writeNum($connection, $serverResponse);
                } elseif (is_array($serverResponse)) {
                    $this->encoder->writeStrings($connection, $serverResponse);
                } elseif (is_string($serverResponse)) {
                    $this->encoder->writeString($connection, $serverResponse);
                }
            }
        } catch (ServerException | ConnectionException $exception) {
            $messageToSend = sprintf('%s: %s', get_class($exception), $exception->getMessage());
            $this->logger->critical($messageToSend);
            $this->encoder->sendError($connection, $messageToSend);
        }
    }
}
