<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\BearerAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class BearerAuthMiddleware implements MiddlewareInterface
{
    private BearerAuthenticator $bearerAuthenticator;
    private ResponseFactory $responseFactory;

    public function __construct(
        BearerAuthenticator $bearerAuthenticator,
        ResponseFactory $responseFactory
    ) {
        $this->bearerAuthenticator = $bearerAuthenticator;
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader[0], 'Bearer ')) {
            return $this->unauthorizedResponse('Bearer token not received');
        }

        $token = str_replace('Bearer ', '', $authHeader[0]);
        if (!$this->bearerAuthenticator->validate($token)) {
            return $this->unauthorizedResponse('Invalid Bearer token');
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
