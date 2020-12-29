<?php

namespace App;

use function GuzzleHttp\Psr7\stream_for;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr7Middlewares\Middleware;
use Psr7Middlewares\Middleware\FormatNegotiator;

class GoogleAnalyticsMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $response = $delegate->process($request);
        $attribute = $request->getAttribute(Middleware::class);
        if (is_array($attribute) && $attribute[FormatNegotiator::KEY] === 'html') {
            $body = (string)$response->getBody();
            $tag = '<ga></ga>';
            $body = str_replace('</body>', $tag . '</body>', $body);
            $body = stream_for($body);

            return $response->withBody($body);
        }
        return $response;

    }
}