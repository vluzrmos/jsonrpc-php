<?php

namespace Vluzrmos\JsonRPC;

class Error
{
    protected $code;
    protected $message;
    protected $data;

    const PARSE_ERROR     = -32700;
    const INVALID_REQUEST = -32600;
    const METHOD_NOT_FOUND = -32601;
    const INVALID_PARAMS  = -32602;
    const INTERNAL_ERROR  = -32603;
    const SERVER_ERROR    = -32000;
    const SERVER_ERROR_LIMIT = -32099;

    const DEFAULT_MESSAGES = [
        self::PARSE_ERROR => 'Parse error',
        self::INVALID_REQUEST => 'Invalid request',
        self::METHOD_NOT_FOUND => 'Method not found',
        self::INVALID_PARAMS => 'Invalid params',
        self::INTERNAL_ERROR => 'Internal error',
        self::SERVER_ERROR => 'Server error',
    ];

    public function __construct($code, $message, $data = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }

    public static function parseError($data = null)
    {
        return new self(
            self::PARSE_ERROR,
            self::DEFAULT_MESSAGES[self::PARSE_ERROR],
            $data
        );
    }

    public static function invalidRequest($data = null)
    {
        return new self(
            self::INVALID_REQUEST,
            self::DEFAULT_MESSAGES[self::INVALID_REQUEST],
            $data
        );
    }

    public static function methodNotFound($data = null)
    {
        return new self(
            self::METHOD_NOT_FOUND,
            self::DEFAULT_MESSAGES[self::METHOD_NOT_FOUND],
            $data
        );
    }

    public static function invalidParams($data = null)
    {
        return new self(
            self::INVALID_PARAMS,
            self::DEFAULT_MESSAGES[self::INVALID_PARAMS],
            $data
        );
    }

    public static function internalError($data = null)
    {
        return new self(
            self::INTERNAL_ERROR,
            self::DEFAULT_MESSAGES[self::INTERNAL_ERROR],
            $data
        );
    }

    public static function serverError($data = null)
    {
        return new self(
            self::SERVER_ERROR,
            self::DEFAULT_MESSAGES[self::SERVER_ERROR],
            $data
        );
    }

    public function isServerError()
    {
        return $this->code <= self::SERVER_ERROR && $this->code >= self::SERVER_ERROR_LIMIT;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toArray()
    {
        $error = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];

        $data = $this->getData();

        if (!is_null($data)) {
            $error['data'] = $data;
        }

        return $error;
    }
}
