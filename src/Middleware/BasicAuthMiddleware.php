<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\BasicAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class BasicAuthMiddleware implements MiddlewareInterface
{
    private BasicAuthenticator $basicAuthenticator;
    private ResponseFactory $responseFactory;

    public function __construct(
        BasicAuthenticator $basicAuthenticator,
        ResponseFactory $responseFactory,
    ) {
        $this->basicAuthenticator = $basicAuthenticator;
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader[0], 'Basic ')) {
            return $this->unauthorizedResponse('Basic credentials not received');
        }

        $credentials = base64_decode(str_replace('Basic ', '', $authHeader[0]));
        [$username, $password] = explode(':', $credentials, 2);
        if (!$this->basicAuthenticator->authenticate($username, $password)) {
            return $this->unauthorizedResponse('Invalid Basic credentials');
        }

        return $handler->handle($request);
    }

    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write(json_encode(['error' => $message], JSON_THROW_ON_ERROR));
        return $response;
    }
}
