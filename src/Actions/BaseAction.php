<?php

namespace RedStor\Actions;

use Predis\Client as PredisClient;
use React\EventLoop\LoopInterface;
use RedStor\Client\Decoder;
use RedStor\Client\Encoder;

class BaseAction
{
    /** @var LoopInterface */
    protected $loop;
    /** @var Encoder */
    protected $encoder;
    /** @var Decoder */
    protected $decoder;
    /** @var PredisClient */
    protected $redis;

    public function __construct(
        LoopInterface $loop,
        Encoder $encoder,
        Decoder $decoder,
        PredisClient $redis
    ) {
        $this->loop = $loop;
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->redis = $redis;
    }
}
