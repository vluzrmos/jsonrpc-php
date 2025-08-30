<?php
namespace Vluzrmos\JsonRPC\Concerns;

use Vluzrmos\JsonRPC\Request;

interface Method
{
    /**
     * Get the method name used in the JSON-RPC request
     *
     * @return string
     */
    public function getName();

    /**
     * Execute the method with the given parameters
     *
     * @param array|null $params The parameters for the method
     * @param string|int $id The request ID
     * @return mixed|Response The result of the method execution, an Error or a Response object
     */
    public function execute(Request $request);
}