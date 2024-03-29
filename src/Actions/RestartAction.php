<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

class RestartAction extends BaseAction implements ActionInterface
{
    public function allowAnonymousUse(): bool
    {
        // @todo this is bad. bad bad bad.
        // But we need to have a superadmin level before I can do something more sensible.
        return true;
    }

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
