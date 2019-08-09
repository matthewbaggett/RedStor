<?php

namespace RedStor\Client;

use Predis\Client;
use Predis\Profile\RedisVersion320;
use Predis\Response\ServerException;
use Predis\Response\Status;
use React\Socket\ConnectionInterface;
use RedStor\Redis\PassthruClient;
use ⌬\Log\Logger;

class Passthru{
    const REDIS_SEPERATOR = "\r\n";

    /** @var Client */
    protected $client;

    /** @var Logger */
    protected $logger;

    /** @var Encoder */
    protected $encoder;

    public function getPassthruCommands() : array
    {
        $version = new RedisVersion320();
        return array_keys($version->getSupportedCommands());
    }

    public function __construct(
        Client $client,
        Logger $logger,
        Encoder $encoder
    )
    {
        $this->client = $client;
        $this->logger = $logger;
        $this->encoder = $encoder;
    }

    public function passthru(ConnectionInterface $connection, $passthruCommand)
    {
        #\Kint::dump($passthruCommand);
        $command = array_shift($passthruCommand);
        try {
            $serverResponse = $this->client->__call($command, $passthruCommand);
            #\Kint::dump($serverResponse);
            if ($serverResponse instanceof Status) {
                $connection->write("+OK" . self::REDIS_SEPERATOR);
            } else {
                if(is_numeric($serverResponse)) {
                    $this->encoder->writeNum($connection, $serverResponse);
                }elseif(is_array($serverResponse)) {
                    $this->encoder->writeStrings($connection, $serverResponse);
                }elseif(is_string($serverResponse)) {
                    $this->encoder->writeString($connection, $serverResponse);
                }
            }
        }catch(ServerException $exception){
            $connection->write("-{$exception->getMessage()}" . self::REDIS_SEPERATOR);
        }
    }

}