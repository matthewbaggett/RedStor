<?php

namespace RedStor\Tests;

use Predis\Client;
use RedStor\SDK\RedStorClient;
use ⌬\Tests\TestCase;

abstract class RedStorTest extends TestCase
{
    /** @var RedStorClient */
    protected $redis;

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new Client([
            'host' => 'redstor',
        ]);
    }
}
