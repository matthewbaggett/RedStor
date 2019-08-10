<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;
use RedStor\RedStor;

class ModelCreateAction extends BaseAction implements ActionInterface
{
    public function getCommand(): string
    {
        return 'MODELCREATE';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        $modelName = $parsedData[1];
        $this->redis->sadd(RedStor::KEY_MODEL_LIST_SET, [$modelName]);
        $this->encoder->sendOK($connection);
    }
}
