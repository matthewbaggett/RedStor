<?php

namespace RedStor\SDK\Entities;

use Predis\Client as PredisClient;

interface EntityInterface extends \JsonSerializable
{
    public static function Factory();

    public function create(PredisClient $redis);

    public function delete(PredisClient $redis);
}
