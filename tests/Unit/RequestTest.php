<?php

use Vluzrmos\JsonRPC\Request;

class RequestTest extends TestCase {
    public function testIsNotificationWithNullId()
    {
        $request = new Request('someMethod', ['param1' => 'value1'], '');

        $this->assertTrue($request->isNotification());
        $this->assertEquals('someMethod', $request->getMethod());
        $this->assertEquals(['param1' => 'value1'], $request->getParams());
        $this->assertTrue($request->isNotification());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2.0', $request->getJsonRPC());

        $this->assertEquals(null, $request->getId());

        $this->assertEquals(
            [
                'jsonrpc' => '2.0',
                'method'  => 'someMethod',
                'params'  => ['param1' => 'value1'],
            ],
            $request->toArray()
        );
    }

    public function testToJson()
    {
        $request = new Request('foo', ['bar' => 'baz'], 42);
        $json = json_encode($request->toArray());
        $this->assertJson($json);
        $this->assertContains('"bar":"baz"', $json);
    }

    public function testArrayAccessNonexistentKey()
    {
        $request = new Request('foo', [], 1);
        $this->assertNull($request['nonexistent']);
    }

    public function testRequestWithoutParams()
    {
        $request = new Request('foo', [], 1);
        $array = $request->toArray();
        $this->assertArrayNotHasKey('params', $array);
    }

    public function testWithStringId()
    {
        $request = new Request('foo', [], 'string-id');
        $this->assertEquals('string-id', $request->getId());
        $this->assertEquals('string-id', $request->toArray()['id']);
    }

    public function testWithFloatId()
    {
        $request = new Request('foo', [], 3.14);
        $this->assertEquals(3.14, $request->getId());
        $this->assertEquals(3.14, $request->toArray()['id']);
    }

    public function testImmutabilityOffsetSet()
    {
        $request = new Request('foo', [], 1);
        $this->expectException(\BadMethodCallException::class);
        $request['id'] = 2;
    }

    public function testImmutabilityOffsetUnset()
    {
        $request = new Request('foo', [], 1);
        $this->expectException(\BadMethodCallException::class);
        unset($request['id']);
    }

    public function testCustomVersion()
    {
        $request = new Request('foo', [], 1, '1.0');
        $this->assertEquals('1.0', $request->getVersion());
        $this->assertEquals('1.0', $request->getJsonRPC());
    }

    public function testIsNotificationWithEmptyStringId()
    {
        $request = new Request('someMethod', ['param1' => 'value1'], '');

        $this->assertTrue($request->isNotification());
        $this->assertTrue($request->isNotification());
        $this->assertEquals('someMethod', $request->getMethod());
        $this->assertEquals(['param1' => 'value1'], $request->getParams());
        $this->assertTrue($request->isNotification());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2.0', $request->getJsonRPC());
        $this->assertEquals(null, $request->getId());
        $this->assertEquals(
            [
                'jsonrpc' => '2.0',
                'method'  => 'someMethod',
                'params'  => ['param1' => 'value1'],
            ],
            $request->toArray()
        );
    }

    public function testIsNotificationWithNonEmptyId()
    {
        $request = new Request('someMethod', ['param1' => 'value1'], 123);

        $this->assertFalse($request->isNotification());
        $this->assertEquals('someMethod', $request->getMethod());
        $this->assertEquals(['param1' => 'value1'], $request->getParams());
        $this->assertEquals('2.0', $request->getVersion());
        $this->assertEquals('2.0', $request->getJsonRPC());
        $this->assertEquals(123, $request->getId());
        $this->assertEquals(
            [
                'jsonrpc' => '2.0',
                'method'  => 'someMethod',
                'params'  => ['param1' => 'value1'],
                'id'      => 123,
            ],
            $request->toArray()
        );
    }
}
