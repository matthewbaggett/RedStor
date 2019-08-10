<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;
use RedStor\RedStor;

class ModelAddColumn extends BaseAction implements ActionInterface
{
    public function getCommand(): string
    {
        return 'MODELADDCOLUMN';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        $command = array_shift($parsedData);
        $modelName = array_shift($parsedData);
        $columnPairs = array_chunk($parsedData, 2);
        foreach ($columnPairs as $columnPair) {
            list($columnName, $columnType) = $columnPair;
            $weight = $this->redis->zcount(sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName), '-inf', '+inf') + 1;
            //\Kint::dump(
            //    sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName),
            //    $weight,
            //    $columnName
            //);
            $this->redis->zadd(
                sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName),
                $weight,
                $columnName
            );
            $this->redis->mset([
                sprintf(RedStor::KEY_MODEL_COLUMN_DEFINITION, $modelName, $columnName).':name' => $columnName,
                sprintf(RedStor::KEY_MODEL_COLUMN_DEFINITION, $modelName, $columnName).':type' => $columnType,
            ]);
        }
        $this->encoder->sendOK($connection);
    }
}
