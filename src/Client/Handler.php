<?php

namespace RedStor\Client;

use Closure;
use Predis\Client;
use Predis\PredisException;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use RedStor\Actions;
use ⌬\Log\Logger;

class Handler
{
    /** @var LoopInterface */
    protected $loop;
    /** @var LoggerInterface */
    protected $logger;
    /** @var ConnectionInterface */
    protected $connection;
    /** @var Decoder */
    protected $decoder;
    /** @var Encoder */
    protected $encoder;
    /** @var Passthru */
    protected $passthru;
    /** @var Client */
    protected $redis;
    /** @var Actions\ActionInterface[] */
    protected $handlerActions = [];

    public function __construct(
        LoopInterface $loop,
        Logger $logger,
        ConnectionInterface $connection,
        Decoder $decoder,
        Encoder $encoder,
        Passthru $passthru,
        Client $redis
    ) {
        $this->loop = $loop;
        $this->logger = $logger;
        $this->connection = $connection;
        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->passthru = $passthru;
        $this->redis = $redis;
        $this->__attachToConnection();

        $this->handlerActions = [
            new Actions\ModelAddColumn($this->loop, $this->encoder, $this->decoder, $this->redis),
            new Actions\ModelCreateAction($this->loop, $this->encoder, $this->decoder, $this->redis),
            new Actions\ModelDescribeAction($this->loop, $this->encoder, $this->decoder, $this->redis),
            new Actions\PingAction($this->loop, $this->encoder, $this->decoder, $this->redis),
            new Actions\RestartAction($this->loop, $this->encoder, $this->decoder, $this->redis),
        ];
    }

    public function __attachToConnection()
    {
        $this->connection->on('data', Closure::fromCallable([$this, 'receiveClientMessage']));
        $this->connection->on('error', Closure::fromCallable([$this, 'handleClientException']));
        $this->connection->on('end', Closure::fromCallable([$this, 'endClient']));
        $this->connection->on('close', Closure::fromCallable([$this, 'closeClient']));
    }

    protected function receiveClientMessage($data)
    {
        try {
            $debugData = str_replace("\n", '\\n', $data);
            $debugData = str_replace("\r", '\\r', $debugData);

            // Fast return for PING.
            if ('PING' == trim($data)) {
                $this->encoder->sendInline($this->connection, '+PING');

                return;
            }

            $parsedData = $this->decoder->decode($data);

            $displayableData = trim(implode(' ', $parsedData));
            if (in_array($displayableData, ['PING'], true)) {
                // suppress these messages.
            } else {
                $this->logger->info(sprintf(
                    '[%s] => %s (%s)',
                    $this->connection->getRemoteAddress(),
                    $displayableData,
                    trim($debugData),
                ));
            }

            if (in_array($parsedData[0], $this->passthru->getPassthruCommands(), true)) {
                $this->passthru->passthru($this->connection, $parsedData);

                return;
            }

            foreach ($this->handlerActions as $handlerAction) {
                if ($handlerAction->getCommand() == $parsedData[0]) {
                    $handlerAction->handle($this->connection, $parsedData);

                    return;
                }
            }

            $this->encoder->sendError($this->connection, sprintf('Sorry, %s is not a valid command.', $parsedData[0]));
        } catch (PredisException $exception) {
            $this->logger->critical(
                sprintf(
                    'Exception %s: %s',
                    get_class($exception),
                    $exception->getMessage()
                )
            );
        }
    }

    protected function endClient()
    {
    }

    protected function closeClient()
    {
    }

    protected function handleClientException(\Exception $e)
    {
    }
}
