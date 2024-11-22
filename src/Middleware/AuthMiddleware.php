<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class AuthMiddleware implements MiddlewareInterface
{
    private BearerAuthMiddleware $bearerAuthMiddleware;
    private BasicAuthMiddleware $basicAuthMiddleware;
    private ResponseFactory $responseFactory;

    public function __construct(
        BearerAuthMiddleware $bearerAuthMiddleware,
        BasicAuthMiddleware $basicAuthMiddleware,
        ResponseFactory $responseFactory,
    ) {
        $this->bearerAuthMiddleware = $bearerAuthMiddleware;
        $this->basicAuthMiddleware = $basicAuthMiddleware;
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader)) {
            return $this->unauthorizedResponse('Authorization header not received');
        }

        if (str_starts_with($authHeader[0], 'Bearer ')) {
            return $this->bearerAuthMiddleware->process($request, $handler);
        }

        if (str_starts_with($authHeader[0], 'Basic ')) {
            return $this->basicAuthMiddleware->process($request, $handler);
        }

        return $this->unauthorizedResponse('Unsupported authorization method');
    }

    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(401);
        $response->getBody()->write(json_encode(['error' => $message], JSON_THROW_ON_ERROR));
        return $response;
    }
}
