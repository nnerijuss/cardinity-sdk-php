<?php

namespace Cardinity\Tests;

use Cardinity\Client;
use Cardinity\Method\Payment;
use Cardinity\Exception\InvalidAttributeValue;

class ErrorTest extends ClientTestCase
{
    public function testErrorResultObjectForErrorResponse()
    {
        $method = $this
            ->getMockBuilder('Cardinity\Method\Payment\Get')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $method->method('getAction')->willReturn('payments');
        $method->method('getAttributes')->willReturn([]);
        $method->method('getMethod')->willReturn(Payment\Get::POST);

        try {
            $this->client->call($method);
        } catch (\Cardinity\Exception\ValidationFailed $e) {
            $result = $e->getResult();
            $this->assertInstanceOf('Cardinity\Method\Error', $result);
            $this->assertSame('https://developers.cardinity.com/api/v1/#400', $result->getType());
            $this->assertSame('Validation Failed', $result->getTitle());
            $this->assertStringContainsString('validation errors', $result->getDetail());
            $this->assertTrue(is_array($result->getErrors()));
            $this->assertNotEmpty($result->getErrors());
        }
    }

    public function testUnauthorizedResponse()
    {
        $this->expectException(\Cardinity\Exception\Unauthorized::class);

        $client = Client::create([
            'consumerKey' => 'no',
            'consumerSecret' => 'yes',
        ]);

        $method = new Payment\Get('xxxyyy');

        $client->call($method);
    }

    public function testBadRequest()
    {
        $this->expectException(\Cardinity\Exception\ValidationFailed::class);

        $method = $this
            ->getMockBuilder('Cardinity\Method\Payment\Get')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $method->method('getAction')->willReturn('payments');
        $method->method('getAttributes')->willReturn([]);
        $method->method('getMethod')->willReturn(Payment\Get::POST);

        $this->client->call($method);
    }

    public function testNotFound()
    {
        $this->expectException(\Cardinity\Exception\NotFound::class);

        $method = $this
            ->getMockBuilder('Cardinity\Method\Payment\Get')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $method->method('getAction')->willReturn('my_dreamy_action');
        $method->method('getAttributes')->willReturn([]);
        $method->method('getMethod')->willReturn(Payment\Get::POST);

        $this->client->call($method);
    }

    public function testMethodNotAllowed()
    {
        $this->expectException(\Cardinity\Exception\MethodNotAllowed::class);

        $method = $this
            ->getMockBuilder('Cardinity\Method\Payment\Get')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $method->method('getAction')->willReturn('payments');
        $method->method('getAttributes')->willReturn([]);
        $method->method('getMethod')->willReturn('DELETE');

        $this->client->call($method);
    }
}
