<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Authentication\JWTAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class JWTAuthMiddleware implements MiddlewareInterface
{
    private JWTAuthenticator $jwtAuthenticator;
    private ResponseFactory $responseFactory;

    public function __construct(
        JWTAuthenticator $jwtAuthenticator,
        ResponseFactory $responseFactory,
    ) {
        $this->jwtAuthenticator = $jwtAuthenticator;
        $this->responseFactory = $responseFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        $authHeader = $request->getHeader('Authorization');
        if (empty($authHeader) || !str_starts_with($authHeader[0], 'JWT ')) {
            return $this->unauthorizedResponse('JWT token not received');
        }

        $token = str_replace('JWT ', '', $authHeader[0]);
        if (!$this->jwtAuthenticator->validate($token)) {
            return $this->unauthorizedResponse('Invalid JWT token');
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
