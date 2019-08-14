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
        @list($columnName, $columnType, $columnOptions) = $parsedData;
        $weight = $this->redis->zcount(sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName), '-inf', '+inf') + 1;
        //\Kint::dump(
        //    sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName),
        //    $weight,
        //    $columnName,
        //    $columnType,
        //    $columnOptions
        //);
        $this->redis->zadd(
            sprintf(RedStor::KEY_MODEL_COLUMN_LIST_SET, $modelName),
            $weight,
            $columnName
        );
        $this->redis->mset([
            sprintf(RedStor::KEY_MODEL_COLUMN_NAME, $modelName, $columnName) => $columnName,
            sprintf(RedStor::KEY_MODEL_COLUMN_TYPE, $modelName, $columnName) => $columnType,
        ]);
        if ($columnOptions) {
            $options = json_decode($columnOptions);
            if (is_array($options) && count($options) > 0) {
                $this->redis->hmset(
                    sprintf(RedStor::KEY_MODEL_COLUMN_OPTIONS, $modelName, $columnName),
                    $options
                );
            }
        }
        $this->encoder->sendOK($connection);
    }
}
