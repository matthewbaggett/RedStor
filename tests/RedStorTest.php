<?php

namespace RedStor\Tests;

use RedStor\SDK\RedStorClient;
use ⌬\Tests\TestCase;

abstract class RedStorTest extends TestCase
{
    /** @var RedStorClient */
    protected $redis;

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new RedStorClient([
            'host' => 'redstor',
        ]);
    }
}
