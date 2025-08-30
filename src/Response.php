<?php

namespace Vluzrmos\JsonRPC;

use ArrayAccess;

class Response implements ArrayAccess
{
    protected $version = '2.0';

    protected $id;

    protected $result;

    /**
     * @var Error
     */
    protected $error;

    public function __construct($result = null, Error $error = null, $id = null, $version = '2.0')
    {
        $this->id = $id;
        $this->result = $result;
        $this->error = $error;
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

    public function getResult()
    {
        return $this->result;
    }

    public function getError()
    {
        return $this->error;
    }

    public static function withSuccess($result, $id = null, $version = '2.0')
    {
        return new self($result, null, $id, $version);
    }

    public static function withError(Error $error, $id = null, $version = '2.0')
    {
        return new self(null, $error, $id, $version);
    }

    public function isError()
    {
        return !is_null($this->getError());
    }

    public function toArray()
    {
        $response = [
            'jsonrpc' => $this->getVersion(),
            'id' => $this->getId(),
        ];

        if (!is_null($error = $this->getError())) {
            $response['error'] = $error->toArray();
        } else {
            $response['result'] = $this->getResult();
        }

        return $response;
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

        return null;
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Not allowed to set values on the response object.');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Not allowed to unset values on the response object.');
    }
}
