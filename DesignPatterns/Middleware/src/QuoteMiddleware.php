<?php

namespace App;

use function GuzzleHttp\Psr7\stream_for;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class QuoteMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);
        $body = stream_for('"' . ((string)$response->getBody()) . '"');

        return $response->withBody($body);
    }
}