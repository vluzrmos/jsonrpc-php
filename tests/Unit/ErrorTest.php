<?php

use Vluzrmos\JsonRPC\Error;

class ErrorTest extends TestCase
{
    public function testConstructAndGetters()
    {
        $error = new Error(123, 'msg', ['foo' => 'bar']);
        $this->assertEquals(123, $error->getCode());
        $this->assertEquals('msg', $error->getMessage());
        $this->assertEquals(['foo' => 'bar'], $error->getData());
    }

    public function testParseErrorStatic()
    {
        $error = Error::parseError('data');
        $this->assertEquals(Error::PARSE_ERROR, $error->getCode());
        $this->assertEquals('Parse error', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testInvalidRequestStatic()
    {
        $error = Error::invalidRequest('data');
        $this->assertEquals(Error::INVALID_REQUEST, $error->getCode());
        $this->assertEquals('Invalid request', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testMethodNotFoundStatic()
    {
        $error = Error::methodNotFound('data');
        $this->assertEquals(Error::METHOD_NOT_FOUND, $error->getCode());
        $this->assertEquals('Method not found', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testInvalidParamsStatic()
    {
        $error = Error::invalidParams('data');
        $this->assertEquals(Error::INVALID_PARAMS, $error->getCode());
        $this->assertEquals('Invalid params', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testInternalErrorStatic()
    {
        $error = Error::internalError('data');
        $this->assertEquals(Error::INTERNAL_ERROR, $error->getCode());
        $this->assertEquals('Internal error', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testServerErrorStatic()
    {
        $error = Error::serverError('data');
        $this->assertEquals(Error::SERVER_ERROR, $error->getCode());
        $this->assertEquals('Server error', $error->getMessage());
        $this->assertEquals('data', $error->getData());
    }

    public function testIsServerErrorTrue()
    {
        $error = new Error(-32000, 'Server error');
        $this->assertTrue($error->isServerError());
    }

    public function testIsServerErrorFalse()
    {
        $error = new Error(-32600, 'Invalid request');
        $this->assertFalse($error->isServerError());
    }

    public function testToArrayWithData()
    {
        $error = new Error(1, 'msg', ['foo' => 'bar']);
        $array = $error->toArray();
        $this->assertEquals(1, $array['code']);
        $this->assertEquals('msg', $array['message']);
        $this->assertEquals(['foo' => 'bar'], $array['data']);
    }

    public function testToArrayWithoutData()
    {
        $error = new Error(2, 'msg');
        $array = $error->toArray();
        $this->assertEquals(2, $array['code']);
        $this->assertEquals('msg', $array['message']);
        $this->assertArrayNotHasKey('data', $array);
    }
}
