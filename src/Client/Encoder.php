<?php

namespace RedStor\Client;

use React\Socket\ConnectionInterface;
use ⌬\Log\Logger;

class Encoder
{
    public const REDIS_SEPERATOR = "\r\n";

    /** @var Logger */
    protected $logger;

    public function __construct(
        Logger $logger
    ) {
        $this->logger = $logger;
    }

    public function write(ConnectionInterface $connection, $data)
    {
        $debugData = str_replace("\n", '\\n', $data);
        $debugData = str_replace("\r", '\\r', $debugData);
        $decodedData = (new Decoder())->decode($data);
        $displayableData = is_array($decodedData) ? implode(' ', $decodedData) : "\"{$decodedData}\"";
        if (in_array(trim($displayableData), ['PING'], true)) {
            $this->logger->info(sprintf(
                "[%s] <= %s (%s)\n",
                $connection->getRemoteAddress(),
                $displayableData,
                trim($debugData)
            ));
        }
        $connection->write($data);
    }

    public function sendError(ConnectionInterface $connection, string $message): void
    {
        $this->writeStrings($connection, ["-ERR {$message}"]);
    }

    public function sendPong(ConnectionInterface $connection, string $message = null): void
    {
        $this->writeStrings($connection, ['PONG', $message]);
    }

    public function sendInline(ConnectionInterface $connection, string $message): void
    {
        $this->write($connection, "+{$message}".self::REDIS_SEPERATOR);
    }

    public function writeStrings(ConnectionInterface $connection, array $strings): void
    {
        $strings = array_filter($strings);
        $output = '*'.count($strings).self::REDIS_SEPERATOR;
        foreach ($strings as $string) {
            $output .= '$'.strlen($string).self::REDIS_SEPERATOR.$string.self::REDIS_SEPERATOR;
        }
        $this->write($connection, $output);
    }

    public function writeString(ConnectionInterface $connection, string $string): void
    {
        $this->write($connection, '$'.strlen($string).self::REDIS_SEPERATOR.$string.self::REDIS_SEPERATOR);
    }

    public function writeNum(ConnectionInterface $connection, float $num): void
    {
        if (is_int($num)) {
            $this->writeInt($connection, $num);
        } else {
            $this->write($connection, '$'.strlen($num).self::REDIS_SEPERATOR.$num.self::REDIS_SEPERATOR);
        }
    }

    public function writeInt(ConnectionInterface $connection, int $int): void
    {
        $this->write($connection, ':'.$int.self::REDIS_SEPERATOR);
    }

    public function writeBlocks(ConnectionInterface $connection, array $blocks): void
    {
        $blocks = array_filter($blocks);
        $string = '';
        foreach ($blocks as $block) {
            $string .= "{$block}".self::REDIS_SEPERATOR;
        }
        $this->write($connection, $string);
    }
}
