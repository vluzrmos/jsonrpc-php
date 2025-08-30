<?php

use Vluzrmos\JsonRPC\Concerns\Method;
use Vluzrmos\JsonRPC\Request;

class MockMethod implements Method
{
    public function getName()
    {
        return 'mockMethod';
    }

    public function execute(Request $request)
    {
        return 'mocked result';
    }
}

class MockMethodGetsParams implements Method
{
    public function getName()
    {
        return 'mockMethodGetsParams';
    }

    public function execute(Request $request)
    {
        return $request->getParams();
    }
}

class MethodTest extends TestCase
{
    public function testGetName()
    {
        $method = new MockMethod();

        $this->assertEquals('mockMethod', $method->getName());
    }

    public function testExecute()
    {
        $method = new MockMethod();
        $request = new Request('mockMethod', ['param1' => 'value1'], 1);

        $result = $method->execute($request);

        $this->assertEquals('mocked result', $result);
    }

    public function testExecuteWithDifferentRequest()
    {
        $method = new MockMethod();
        $request = new Request('mockMethod', ['paramA' => 'valueA'], 42);

        $result = $method->execute($request);

        $this->assertEquals('mocked result', $result);
    }

    public function testExecuteGetsParams()
    {
        $method = new MockMethodGetsParams();
        $params = ['key1' => 'value1', 'key2' => 'value2'];
        $request = new Request('mockMethodGetsParams', $params, 2);

        $result = $method->execute($request);

        $this->assertEquals($params, $result);
    }

    public function testExecuteReturnsNull()
    {
        $method = new MethodReturnsNull();
        $request = new Request('nullMethod', [], 1);
        $this->assertNull($method->execute($request));
    }

    public function testExecuteReturnsArray()
    {
        $method = new MethodReturnsArray();
        $request = new Request('arrayMethod', [], 1);
        $this->assertEquals(['foo' => 'bar'], $method->execute($request));
    }

    public function testExecuteWithStringId()
    {
        $method = new MockMethodGetsParams();
        $params = ['x' => 1];
        $request = new Request('mockMethodGetsParams', $params, 'string-id');
        $result = $method->execute($request);
        $this->assertEquals($params, $result);
        $this->assertEquals('string-id', $request->getId());
    }

    public function testExecuteWithFloatId()
    {
        $method = new MockMethodGetsParams();
        $params = ['y' => 2];
        $request = new Request('mockMethodGetsParams', $params, 3.14);
        $result = $method->execute($request);
        $this->assertEquals($params, $result);
        $this->assertEquals(3.14, $request->getId());
    }

    public function testExecuteWithNullId()
    {
        $method = new MockMethodGetsParams();
        $params = ['z' => 3];
        $request = new Request('mockMethodGetsParams', $params, null);
        $result = $method->execute($request);
        $this->assertEquals($params, $result);
        $this->assertNull($request->getId());
    }

    public function testExecuteDynamicReturn()
    {
        $method = new MethodDynamicReturn();
        $request = new Request('dynamicMethod', ['a' => 1], 99);
        $result = $method->execute($request);
        $this->assertEquals('dynamicMethod:{"a":1}', $result);
    }
}

class MethodReturnsNull implements Method
{
    public function getName()
    {
        return 'nullMethod';
    }
    public function execute(Request $request)
    {
        return null;
    }
}

class MethodReturnsArray implements Method
{
    public function getName()
    {
        return 'arrayMethod';
    }
    public function execute(Request $request)
    {
        return ['foo' => 'bar'];
    }
}

class MethodDynamicReturn implements Method
{
    public function getName()
    {
        return 'dynamicMethod';
    }
    public function execute(Request $request)
    {
        return $request->getMethod() . ':' . json_encode($request->getParams());
    }
}
