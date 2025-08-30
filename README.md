# JSON-RPC PHP 5.6 Library

A simple and flexible PHP library to implement JSON-RPC 2.0 servers and clients. Supports PHP 5.6+, PSR-4 autoloading, and PHPUnit tests.

## Features
- JSON-RPC 2.0 protocol support
- Easy method registration and execution
- Error handling according to the spec
- Batch requests support
- Compatible with PHP 5.6 and above

## Requirements
- PHP >= 5.6
- Composer

## Installation

```bash
composer require vluzrmos/jsonrpc
```

## Usage Example

### Registering Methods
```php
use Vluzrmos\JsonRPC\Server;
use Vluzrmos\JsonRPC\Request;
use Vluzrmos\JsonRPC\Concerns\Method;

class SumMethod implements Method {
    public function getName() { return 'sum'; }
    public function execute(Request $request) {
        $params = $request->getParams();
        return array_sum($params);
    }
}

$server = new Server();
$server->addMethod(new SumMethod());
```

### Handling a JSON-RPC Request
```php
$json = '{"jsonrpc":"2.0","method":"sum","params":[1,2,3],"id":1}';
$response = $server->reply($json);

// $response is an instance of Response
print_r($response->toArray());
// Output:
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 6
//     [id] => 1
// )
```

### Batch Requests
```php
$batch = '[
    {"jsonrpc":"2.0","method":"sum","params":[1,2],"id":1},
    {"jsonrpc":"2.0","method":"sum","params":[5,7],"id":2}
]';
$responses = $server->reply($batch);

foreach ($responses as $response) {
    print_r($response->toArray());
}
// Output:
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 3
//     [id] => 1
// )
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 12
//     [id] => 2
// )
```

### Notification (No Response)
```php
// Single notification (no 'id' field)
$json = '{"jsonrpc":"2.0","method":"sum","params":[1,2,3]}';
$response = $server->reply($json); // $response will be null
// Output:
// null
```

### Batch Notification
```php
$batch = '[
    {"jsonrpc":"2.0","method":"sum","params":[1,2]},
    {"jsonrpc":"2.0","method":"sum","params":[5,7]}
]';
$responses = $server->reply($batch); // $responses will be an empty array or null
// Output:
// [] ou null
```

## Usage with Request Objects

### Single Request Object
```php
use Vluzrmos\JsonRPC\Request;

$request = new Request('sum', [10, 20, 30], 99);
$response = $server->reply($request);
print_r($response->toArray());
// Output:
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 60
//     [id] => 99
// )
```

### Request with Named Parameters
```php
$request = new Request('sum', ['a' => 5, 'b' => 7], 'custom-id');
$response = $server->reply($request);
print_r($response->toArray());
// Output:
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 12
//     [id] => custom-id
// )
```

### Batch Requests with Request Objects
```php
$requests = [
    new Request('sum', [1, 2], 1),
    new Request('sum', [3, 4], 2)
];
$responses = $server->reply($requests);
foreach ($responses as $response) {
    print_r($response->toArray());
}
// Output:
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 3
//     [id] => 1
// )
// Array
// (
//     [jsonrpc] => 2.0
//     [result] => 7
//     [id] => 2
// )
```

## Running Tests

```bash
docker build -t jsonrpc-php56 .
docker run -it --rm jsonrpc-php56 php ./vendor/bin/phpunit
```

## License
MIT
