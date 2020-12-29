<?php
require 'vendor/autoload.php';

use function Http\Response\send;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$trailingSlash = function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    $url = (string)$request->getUri();
    if (!empty($url) && $url[-1] === '/') {
        $response = new \GuzzleHttp\Psr7\Response();
        return $response
            ->withHeader('Location', substr($url, 0, -1))
            ->withStatus(301);
    }
    return $next($request, $response);
};

$app = function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    $url = $request->getUri()->getPath();
    if ($url === '/blog') {
        $response->getBody()->write('Je suis sur le blog');
    } elseif ($url === '/contact') {
        $response->getBody()->write('<body>Me contacter</body>');
    } else {
        $response->getBody()->write('Not found');
        $response = $response->withStatus(404);
    }
    return $response;
};

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = new \GuzzleHttp\Psr7\Response();

$dispatcher = new \App\Dispatcher();
$dispatcher->pipe(new \Middlewares\Whoops());
$dispatcher->pipe($trailingSlash);
$dispatcher->pipe(\Psr7Middlewares\Middleware::formatNegotiator());
$dispatcher->pipe(new \App\GoogleAnalyticsMiddleware());
$dispatcher->pipe($app);

send($dispatcher->process($request, $response));