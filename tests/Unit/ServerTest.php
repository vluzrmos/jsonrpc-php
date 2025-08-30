<?php

use Vluzrmos\JsonRPC\Server;
use Vluzrmos\JsonRPC\Request;
use Vluzrmos\JsonRPC\Response;
use Vluzrmos\JsonRPC\Error;
use Vluzrmos\JsonRPC\Concerns\Method;

class DummyMethod implements Method
{
    public function getName() { return 'dummy'; }
    public function execute(Request $request) { return 'ok'; }
}

class DummyErrorMethod implements Method
{
    public function getName() { return 'error'; }
    public function execute(Request $request) { return new Error(-32000, 'error'); }
}

class ServerTest extends TestCase
{
    public function testAddAndGetMethod()
    {
        $server = new Server();
        $method = new DummyMethod();
        $server->addMethod($method);
        $this->assertTrue($server->hasMethod('dummy'));
        $this->assertSame($method, $server->getMethod('dummy'));
    }

    public function testRemoveMethod()
    {
        $server = new Server();
        $method = new DummyMethod();
        $server->addMethod($method);
        $server->removeMethod('dummy');
        $this->assertFalse($server->hasMethod('dummy'));
    }

    public function testClearMethods()
    {
        $server = new Server();
        $server->addMethod(new DummyMethod());
        $server->clearMethods();
        $this->assertEmpty($server->getMethods());
    }

    public function testReplyWithValidRequest()
    {
        $server = new Server();
        $server->addMethod(new DummyMethod());
        $request = new Request('dummy', [], 1);
        $response = $server->reply($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('ok', $response->getResult());
        $this->assertEquals(1, $response->getId());
    }

    public function testReplyWithInvalidMethod()
    {
        $server = new Server();
        $request = new Request('notfound', [], 2);
        $response = $server->reply($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(Error::class, $response->getError());
        $this->assertEquals(-32601, $response->getError()->getCode());
    }

    public function testReplyWithErrorMethod()
    {
        $server = new Server();
        $server->addMethod(new DummyErrorMethod());
        $request = new Request('error', [], 3);
        $response = $server->reply($request);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(Error::class, $response->getError());
        $this->assertEquals(-32000, $response->getError()->getCode());
    }

    public function testReplyWithInvalidRequestObject()
    {
        $server = new Server();
        $response = $server->reply(['foo' => 'bar']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(Error::class, $response->getError());
        $this->assertEquals(-32600, $response->getError()->getCode());
        $this->assertNull($response->getId());
        $this->assertEquals('Invalid request', $response->getError()->getMessage());
    }

    public function testReplyWithJsonString()
    {
        $server = new Server();
        $server->addMethod(new DummyMethod());
        $json = json_encode(['jsonrpc' => '2.0', 'method' => 'dummy', 'id' => 10]);
        $response = $server->reply($json);
        
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('ok', $response->getResult());
        $this->assertEquals(10, $response->getId());
    }

    public function testBatchRequests()
    {
        $server = new Server();
        $server->addMethod(new DummyMethod());
        $requests = [
            new Request('dummy', [], 1),
            new Request('dummy', [], 2)
        ];
        $responses = $server->reply($requests);

        $this->assertTrue(is_array($responses));
        $this->assertCount(2, $responses);
        $this->assertEquals(1, $responses[0]->getId());
        $this->assertEquals(2, $responses[1]->getId());
    }
}
