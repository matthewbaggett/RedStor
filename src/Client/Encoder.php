<?php

namespace RedStor\Client;

use React\Socket\ConnectionInterface;

class Encoder{
    const REDIS_SEPERATOR = '\r\n';

    public function sendPong(ConnectionInterface $connection, string $message = null){
        if($message){
            $connection->write("+PONG" . self::REDIS_SEPERATOR);
        }else{
            $connection->write("+PONG" . self::REDIS_SEPERATOR);
        }
    }
}