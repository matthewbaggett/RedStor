<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

class ModelDescribeAction extends BaseAction implements ActionInterface
{
    public function getCommand(): string
    {
        return 'MODELDESCRIBE';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        \Kint::dump($parsedData);
    }
}
