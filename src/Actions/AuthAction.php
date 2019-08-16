<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;

class AuthAction extends BaseAction implements ActionInterface
{
    public function getCommand(): string
    {
        return 'AUTH';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        list($method, $credentials) = $parsedData;
        list($appname, $username, $password) = explode(':', $credentials, 3);
        \Kint::dump($appname, $username, $password);
        $this->encoder->sendInline(
            $connection,
            '+AUTH herple derple'
        );
    }
}
