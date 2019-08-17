<?php

namespace RedStor\Actions;

use React\Socket\ConnectionInterface;
use RedStor\RedStor;

class AuthAction extends BaseAction implements ActionInterface
{
    public function allowAnonymousUse(): bool
    {
        return true;
    }

    public function getCommand(): string
    {
        return 'AUTH';
    }

    public function handle(ConnectionInterface $connection, $parsedData): void
    {
        list($method, $credentials) = $parsedData;
        list($appname, $username, $password) = explode(':', $credentials, 3);
        \Kint::dump($appname, $username, $password);
        $authMatching = $this->redis->hget(
            sprintf(RedStor::KEY_AUTH_APP, $appname),
            $username
        );
        \Kint::dump($authMatching);
        if (password_verify($password, $authMatching)) {
            if (password_needs_rehash($authMatching, PASSWORD_DEFAULT)) {
                $this->redis->hset(
                    sprintf(RedStor::KEY_AUTH_APP, $appname),
                    $username,
                    password_hash($password, PASSWORD_DEFAULT)
                );
            }

            $this->getHandler()->getState()
                ->setLoggedInApp($appname)
                ->setLoggedInUser($username)
            ;

            $this->encoder->sendInline(
                $connection,
                "+AUTH Hello {$username}!"
            );
        } else {
            $this->encoder->sendInline(
                $connection,
                '-AUTH Credentials invalid.'
            );
        }
    }
}
