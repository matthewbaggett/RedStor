<?php

namespace RedStor\Actions;

use Predis\Client as PredisClient;
use React\EventLoop\LoopInterface;
use RedStor\Client\Decoder;
use RedStor\Client\Encoder;
use RedStor\Client\Handler;

class BaseAction
{
    /** @var Handler */
    protected $handler;
    /** @var LoopInterface */
    protected $loop;
    /** @var Encoder */
    protected $encoder;
    /** @var Decoder */
    protected $decoder;
    /** @var PredisClient */
    protected $redis;

    public function __construct(
        Handler $handler
    ) {
        $this->handler = $handler;
        $this->loop = $handler->getLoop();
        $this->encoder = $handler->getEncoder();
        $this->decoder = $handler->getDecoder();
        $this->redis = $handler->getRedis();
    }
}
