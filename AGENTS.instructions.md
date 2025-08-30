# Agent instructions file

# Project Overview
This project is a PHP library that implements the JSON-RPC 2.0 protocol, allowing developers to create and consume JSON-RPC services easily. It provides a simple and flexible way to handle remote procedure calls over HTTP or other transport mechanisms.


# Project requirements

- Supports php 5.6 and above
- Uses Composer for dependency management
- Follows PSR-4 autoloading standard
- Includes unit tests using PHPUnit
- Adheres to PSR-12 coding standards
- Provides clear documentation and examples
- Project namespace is `Vluzrmos\JSONRPC`

# Docker

Requires a Dockerfile that:
- Uses an official PHP Docker image as the base
- Installs necessary PHP extensions
- Sets up Composer within the Docker container
- Configures the working directory and copies project files

Run:

```bash
docker build -t jsonrpc-php56 .
docker run -it --rm jsonrpc-php56 php ./vendor/bin/phpunit
```