<?php

namespace Vluzrmos\JsonRPC;

use ArrayAccess;

class Request implements ArrayAccess
{
    protected $version = '2.0';
    protected $id;
    protected $method;
    protected $params = [];

    public function __construct($method, $params = [], $id = null, $version = '2.0')
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
        $this->version = $version;
    }

    public function getId()
    {
        if (is_null($this->id) || $this->id === '') {
            return null;
        }

        return $this->id;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getJsonRPC()
    {
        return $this->getVersion();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function toArray()
    {
        $request = [
            'jsonrpc' => $this->getVersion(),
            'method'  => $this->getMethod(),
        ];

        if ($params = $this->getParams()) {
            $request['params'] = $params;
        }

        $id = $this->getId();

        if (!is_null($id) && $id !== '') {
            $request['id'] = $id;
        }

        return $request;
    }

    public function isNotification()
    {
        $id = $this->getId();

        return is_null($id) || $id === '';
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset)
    {
        $array = $this->toArray();

        if (array_key_exists($offset, $array)) {
            return $array[$offset];
        }
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Cannot modify immutable object');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Cannot modify immutable object');
    }


}
