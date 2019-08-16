<?php

namespace RedStor\Client;

class State
{
    /** @var string */
    protected $loggedInApp;
    /** @var string */
    protected $loggedInUser;

    /**
     * @return string
     */
    public function getLoggedInApp(): string
    {
        return $this->loggedInApp;
    }

    /**
     * @param string $loggedInApp
     *
     * @return State
     */
    public function setLoggedInApp(string $loggedInApp): State
    {
        $this->loggedInApp = $loggedInApp;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoggedInUser(): string
    {
        return $this->loggedInUser;
    }

    /**
     * @param string $loggedInUser
     *
     * @return State
     */
    public function setLoggedInUser(string $loggedInUser): State
    {
        $this->loggedInUser = $loggedInUser;

        return $this;
    }
}
