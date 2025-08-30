<?php

namespace Vluzrmos\JsonRPC;

use Vluzrmos\JsonRPC\Concerns\Method;

class Server
{
    public $methods = [];

    public function addMethod(Method $method)
    {
        $this->methods[$method->getName()] = $method;

        return $this;
    }

    /**
     * @param string $name
     * @return Method|null
     */
    public function getMethod($name)
    {
        if (array_key_exists($name, $this->methods)) {
            return $this->methods[$name];
        }

        return null;
    }

    public function hasMethod($name)
    {
        return array_key_exists($name, $this->methods);
    }

    public function removeMethod($name)
    {
        unset($this->methods[$name]);

        return $this;
    }

    public function clearMethods()
    {
        $this->methods = [];

        return $this;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string|array|Request $request The JSON-RPC request as a JSON string, an array of a single or batch requests, or a Request object
     * @return Response|Response[]|null
     */
    public function reply($request)
    {
        $request = $this->parseRequest($request);

        if ($request instanceof Error) {
            return Response::withError($request);
        }

        if ($request instanceof Response) {
            return $request;
        }

        if (is_array($request)) {
            return $this->handleBatch($request);
        }

        return $this->handleRequest($request);
    }

    protected function parseRequest($request)
    {
        if ($request instanceof Request) {
            if ($request->getJsonRPC() !== '2.0') {
                return Response::withError(Error::invalidRequest('Invalid JSON-RPC version'), $request->getId(), '2.0');
            }

            if (empty($request->getMethod())) {
                return Response::withError(Error::invalidRequest('The method is required'), $request->getId(), '2.0');
            }

            return $request;
        }

        if (is_string($request)) {
            $request = json_decode($request, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return Error::parseError(json_last_error_msg());
            }

            if (empty($request)) {
                return Error::invalidRequest('The request is empty');
            }

            return $this->parseRequest($request);
        }

        if (is_array($request) && isset($request['method'])) {
            $request = new Request(
                isset($request['method']) ? $request['method'] : null,
                isset($request['params']) ? $request['params'] : [],
                isset($request['id']) ? $request['id'] : null,
                isset($request['jsonrpc']) ? $request['jsonrpc'] : '2.0'
            );

            if ($request->getJsonRPC() !== '2.0') {
                return Response::withError(Error::invalidRequest('Invalid JSON-RPC version'), $request->getId(), '2.0');
            }

            if (empty($request->getMethod())) {
                return Response::withError(Error::invalidRequest('The method is required'), $request->getId(), '2.0');
            }

            return $request;
        }

        else if (is_array($request) && isset($request[0]['method'])) {
            $requests = [];

            foreach ($request as $req) {
                $req = $this->parseRequest($req);

                if ($req instanceof Error) {
                    return $req;
                }

                if ($req instanceof Response) {
                    return $req;
                }

                $requests[] = $req;
            }

            return $requests;
        }

        return Error::invalidRequest('Invalid request object');
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    protected function handleRequest(Request $request)
    {
        $methodName = $request->getMethod();

        if (empty($methodName) || !is_string($methodName) || !$this->hasMethod($methodName)) {
            return Response::withError(
                Error::methodNotFound(),
                $request->getId()
            );
        }

        $method = $this->getMethod($request->getMethod());

        try {
            $result = $method->execute($request);

            if ($request->isNotification()) {
                return null;
            }

            if ($result instanceof Response) {
                return $result;
            }

            if ($result instanceof Error) {
                return Response::withError(
                    $result,
                    $request->getId(),
                    $request->getJsonRPC()
                );
            }

            return Response::withSuccess(
                $result,
                $request->getId(),
                $request->getJsonRPC()
            );
        } catch (\InvalidArgumentException $e) {
            if ($request->isNotification()) {
                return null;
            }

            return Response::withError(
                Error::invalidParams(),
                $request->getId(),
                $request->getJsonRPC()
            );
        } catch (\Exception $e) {
            if ($request->isNotification()) {
                return null;
            }

            return Response::withError(
                Error::internalError(),
                $request->getId(),
                $request->getJsonRPC()
            );
        }
    }

    /**
     * @param Request[]|array<int,Request> $requests
     * @return Response|Response[]|null
     */
    public function handleBatch(array $requests)
    {
        $responses = [];

        if (empty($requests)) {
            return Response::withError(
                Error::invalidRequest()
            );
        }

        foreach ($requests as $request) {
            $response = $this->handleRequest($request);

            if ($response instanceof Response) {
                $responses[] = $response;
            }
        }

        return $responses;
    }
}
