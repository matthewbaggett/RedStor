<?php

namespace RedStor\Tests\Gateway;

/**
 * @internal
 * @covers \RedStor\Controllers\PingController
 */
class PingTest extends GatewayTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    public function testCreateRaw()
    {
        $response = $this->guzzle->get('/v1/ping');

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('Status', $json);

        $this->assertEquals('Okay', $json['Status']);
    }
}
