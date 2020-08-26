<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Framework;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Server
{
    private $routes = [];

    public static function new(): self
    {
        return new self();
    }

    public function route(string $method, string $path, callable $handler): self
    {
        $this->routes["{$method}:{$path}"] = $handler;

        return $this;
    }

    public function run(): void
    {
        try {
            $router = $this->compileRouter();
            $response = $router(ServerRequest::fromGlobals());
        } catch (Throwable $err) {
            $response = new ErrorResponse("Error: {$err->getMessage()}");
        }

        $this->emitResponse($response);
    }

    private function emitResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $header => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $header, $value), false);
            }
        }

        echo $response->getBody();
    }

    private function compileRouter(): callable
    {
        return function (ServerRequest $request): ResponseInterface {
            $uri = $request->getMethod() . ':' . $request->getUri()->getPath();
            foreach ($this->routes as $route => $handler) {
                if (preg_match("@{$route}@i", $uri, $params)) {
                    return $handler($request->withAttribute('params', $params));
                }
            }

            return new ErrorResponse('Route not found', 404);
        };
    }
}
