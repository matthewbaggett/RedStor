<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

class PingAction extends BaseAction implements ActionInterface
{
    public function allowAnonymousUse(): bool
    {
        return true;
    }

    public function getCommand(): string
    {
        return 'PING';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        $this->encoder->sendPong(
            $connection,
            $parsedData[1] ?? null
        );
    }
}
