<?php

namespace App;

use GuzzleHttp\Psr7\Response;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dispatcher implements DelegateInterface
{

    /**
     * @var array
     */
    private $middlewares = [];

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var Response
     */
    private $response;

    /**
     * Permet d'enregistrer un nouveau middleware
     * @param callable|MiddlewareInterface $middleware
     */
    public function pipe($middleware)
    {
        $this->middlewares[] = $middleware;
        $this->response = new Response();
    }

    /**
     * Permet d'éxécuter les middlewares
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        $this->index++;
        if (is_null($middleware)) {
            return $this->response;
        }
        if (is_callable($middleware)) {
            return $middleware($request, $this->response, [$this, 'process']);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    private function getMiddleware()
    {
        if (isset($this->middlewares[$this->index])) {
            return $this->middlewares[$this->index];
        }
        return null;
    }

}