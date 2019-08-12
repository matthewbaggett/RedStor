<?php

namespace RedStor\Tests\Gateway;

use GuzzleHttp\Client;
use RedStor\Tests\RedStorTest;

abstract class GatewayTest extends RedStorTest
{
    /** @var Client */
    protected $guzzle;

    public function setUp(): void
    {
        parent::setUp();
        $this->guzzle = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://gateway/',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);
    }

}
