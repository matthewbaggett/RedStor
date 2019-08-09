<?php
namespace RedStor\Tests;

use Predis\Client;
use ⌬\Tests\TestCase;

abstract class RedStorTest extends TestCase
{
    /** @var Client */
    protected $redis;

    public function setUp(): void
    {
        parent::setUp();
        $this->redis = new Client([
            'host' => 'redstor',
        ]);
    }


}