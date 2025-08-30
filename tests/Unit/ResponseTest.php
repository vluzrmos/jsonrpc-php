<?php

use Vluzrmos\JsonRPC\Response;

class ResponseTest extends TestCase
{
    public function testWithSuccess()
    {
        $response = Response::withSuccess(['key' => 'value'], 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(1, $response->getId());
        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals('2.0', $response->getJsonRPC());
        $this->assertEquals(['key' => 'value'], $response->getResult());
        $this->assertNull($response->getError());
    }

    public function testWithError()
    {
        $error = new \Vluzrmos\JsonRPC\Error(-32601, 'Method not found');
        $response = Response::withError($error, 1);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(1, $response->getId());
        $this->assertEquals('2.0', $response->getVersion());
        $this->assertEquals('2.0', $response->getJsonRPC());
        $this->assertNull($response->getResult());
        $this->assertInstanceOf(\Vluzrmos\JsonRPC\Error::class, $response->getError());
        $this->assertEquals(-32601, $response->getError()->getCode());
        $this->assertEquals('Method not found', $response->getError()->getMessage());
    }

    public function testGetIdWhenNull()
    {
        $response = Response::withSuccess('result', null);

        $this->assertNull($response->getId());
    }

    public function testGetIdWhenEmptyString()
    {
        $response = Response::withSuccess('result', '');

        $this->assertEquals(null, $response->getId());
    }

    public function testGetIdWhenNonEmpty()
    {
        $response = Response::withSuccess('result', 42);

        $this->assertEquals(42, $response->getId());
    }

    public function testCustomVersion()
    {
        $response = Response::withSuccess('result', 1, '1.0');

        $this->assertEquals('1.0', $response->getVersion());
        $this->assertEquals('1.0', $response->getJsonRPC());
    }

    public function testArrayAccess()
    {
        $response = Response::withSuccess(['key' => 'value'], 1);

        $this->assertTrue(isset($response['jsonrpc']));
        $this->assertTrue(isset($response['result']));
        $this->assertTrue(isset($response['id']));
        $this->assertFalse(isset($response['error']));

        $this->assertEquals('2.0', $response['jsonrpc']);
        $this->assertEquals(['key' => 'value'], $response['result']);
        $this->assertEquals(1, $response['id']);
        $this->assertNull($response['error']);

        $this->assertNull($response['nonexistent']);
    }

    public function testToArray()
    {
        $response = Response::withSuccess(['key' => 'value'], 1);

        $this->assertEquals(
            [
                'jsonrpc' => '2.0',
                'result'  => ['key' => 'value'],
                'id'      => 1,
            ],
            $response->toArray()
        );

        $error = new \Vluzrmos\JsonRPC\Error(-32601, 'Method not found');
        $response = Response::withError($error, 1);

        $this->assertEquals(
            [
                'jsonrpc' => '2.0',
                'error'   => [
                    'code'    => -32601,
                    'message' => 'Method not found',
                ],
                'id'      => 1,
            ],
            $response->toArray()
        );
    }

    public function testIsError()
    {
        $response = Response::withSuccess('result', 1);
        $this->assertFalse($response->isError());

        $error = new \Vluzrmos\JsonRPC\Error(-32601, 'Method not found');
        $response = Response::withError($error, 1);
        $this->assertTrue($response->isError());
    }

    public function testIsErrorWhenNoError()
    {
        $response = Response::withSuccess('result', 1);
        $this->assertFalse($response->isError());
    }

    public function testIsErrorWhenError()
    {
        $error = new \Vluzrmos\JsonRPC\Error(-32601, 'Method not found');
        $response = Response::withError($error, 1);
        $this->assertTrue($response->isError());
    }

    public function testToJson()
    {
        $response = Response::withSuccess(['foo' => 'bar'], 123);
        $json = json_encode($response->toArray());
        $this->assertJson($json);
        $this->assertContains('"foo":"bar"', $json);
    }

    public function testWithErrorWithData()
    {
        $error = new \Vluzrmos\JsonRPC\Error(-32000, 'Custom error', ['extra' => 'info']);
        $response = Response::withError($error, 'abc');
        $array = $response->toArray();
        $this->assertEquals(['extra' => 'info'], $array['error']['data']);
    }

    public function testArrayAccessNonexistentKey()
    {
        $response = Response::withSuccess('ok', 1);
        $this->assertNull($response['nonexistent']);
    }

    public function testResponseWithoutResultAndError()
    {
        $response = new Response(null, null, 1);
        $this->assertNull($response->getResult());
        $this->assertNull($response->getError());
        $this->assertEquals(1, $response->getId());
    }

    public function testWithStringId()
    {
        $response = Response::withSuccess('ok', 'string-id');
        $this->assertEquals('string-id', $response->getId());
    }

    public function testWithFloatId()
    {
        $response = Response::withSuccess('ok', 3.14);
        $this->assertEquals(3.14, $response->getId());
    }
}
