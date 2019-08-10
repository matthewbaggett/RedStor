<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

class RestartAction extends BaseAction implements ActionInterface
{
    public function getCommand(): string
    {
        return 'RESTART';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        $this->encoder->writeStrings($connection, ['+RESTART', 'Server is now restarting']);
        $connection->end();
        $this->loop->addTimer(1.0, function () {
            die("Restarting!\n");
        });
    }
}
