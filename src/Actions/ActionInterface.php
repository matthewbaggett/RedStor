<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

interface ActionInterface
{
    public function allowAnonymousUse(): bool;

    public function getCommand(): string;

    public function handle(ConnectionInterface $connection, $parsedData): void;
}
