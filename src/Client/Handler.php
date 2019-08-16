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
    /** @var State */
    protected $state;

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
            new Actions\AuthAction($this),
            new Actions\ModelAddColumn($this),
            new Actions\ModelCreateAction($this),
            new Actions\ModelDescribeAction($this),
            new Actions\PingAction($this),
            new Actions\RestartAction($this),
        ];

        $this->state = new State();
    }

    public function __attachToConnection()
    {
        $this->connection->on('data', Closure::fromCallable([$this, 'receiveClientMessage']));
        $this->connection->on('error', Closure::fromCallable([$this, 'handleClientException']));
        $this->connection->on('end', Closure::fromCallable([$this, 'endClient']));
        $this->connection->on('close', Closure::fromCallable([$this, 'closeClient']));
    }

    /**
     * @return LoopInterface
     */
    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    /**
     * @param LoopInterface $loop
     *
     * @return Handler
     */
    public function setLoop(LoopInterface $loop): Handler
    {
        $this->loop = $loop;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return Handler
     */
    public function setLogger(LoggerInterface $logger): Handler
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return Handler
     */
    public function setConnection(ConnectionInterface $connection): Handler
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return Decoder
     */
    public function getDecoder(): Decoder
    {
        return $this->decoder;
    }

    /**
     * @param Decoder $decoder
     *
     * @return Handler
     */
    public function setDecoder(Decoder $decoder): Handler
    {
        $this->decoder = $decoder;

        return $this;
    }

    /**
     * @return Encoder
     */
    public function getEncoder(): Encoder
    {
        return $this->encoder;
    }

    /**
     * @param Encoder $encoder
     *
     * @return Handler
     */
    public function setEncoder(Encoder $encoder): Handler
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * @return Passthru
     */
    public function getPassthru(): Passthru
    {
        return $this->passthru;
    }

    /**
     * @param Passthru $passthru
     *
     * @return Handler
     */
    public function setPassthru(Passthru $passthru): Handler
    {
        $this->passthru = $passthru;

        return $this;
    }

    /**
     * @return Client
     */
    public function getRedis(): Client
    {
        return $this->redis;
    }

    /**
     * @param Client $redis
     *
     * @return Handler
     */
    public function setRedis(Client $redis): Handler
    {
        $this->redis = $redis;

        return $this;
    }

    /**
     * @return Actions\ActionInterface[]
     */
    public function getHandlerActions(): array
    {
        return $this->handlerActions;
    }

    /**
     * @param Actions\ActionInterface[] $handlerActions
     *
     * @return Handler
     */
    public function setHandlerActions(array $handlerActions): Handler
    {
        $this->handlerActions = $handlerActions;

        return $this;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    /**
     * @param State $state
     *
     * @return Handler
     */
    public function setState(State $state): Handler
    {
        $this->state = $state;

        return $this;
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

            // Intercept our handlers for our function calls
            foreach ($this->handlerActions as $handlerAction) {
                if ($handlerAction->getCommand() == $parsedData[0]) {
                    $handlerAction->handle($this->connection, $parsedData);

                    return;
                }
            }

            // Pass the rest through to Redis.
            if (in_array($parsedData[0], $this->passthru->getPassthruCommands(), true)) {
                $this->passthru->passthru($this->connection, $parsedData);

                return;
            }

            // No dice? Explode.
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
