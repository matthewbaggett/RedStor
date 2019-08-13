<?php

namespace RedStor\Tests\Gateway;

use GuzzleHttp\Client;
use ⌬\Tests\TestCase;

abstract class GatewayTest extends TestCase
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
            'timeout' => 2.0,
        ]);
    }
}
