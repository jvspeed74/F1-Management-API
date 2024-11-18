<?php

namespace App\Authentication;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\TokenRepository;

class BearerAuthenticator
{
    protected TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Middleware to authenticate users using Bearer token.
     *
     * @param Request $request The HTTP request object
     * @param Response $response The HTTP response object
     * @param callable $next The next middleware or route handler
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        // If the Authorization header is not present, return an error
        if (!$request->hasHeader('Authorization')) {
            $results = [
                'status' => 'Authorization header not available'
            ];
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401)
                ->write(json_encode($results, JSON_PRETTY_PRINT));
        }

        // Retrieve the Authorization header
        $auth = $request->getHeader('Authorization');

        // The value of the Authorization header is in the form "Bearer <token>"
        if (strpos($auth[0], 'Bearer ') !== 0) {
            $results = [
                'status' => 'Invalid Authorization header format'
            ];
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400)
                ->write(json_encode($results, JSON_PRETTY_PRINT));
        }

        // Extract the token by removing the "Bearer " prefix
        $token = substr($auth[0], 7);

        // Validate the Bearer token using TokenRepository
        $tokenRecord = $this->tokenRepository->validateBearerToken($token);
        if (!$tokenRecord) {
            $results = [
                'status' => 'Authentication failed'
            ];
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401)
                ->write(json_encode($results, JSON_PRETTY_PRINT));
        }

        // Continue to the next middleware or handler
        return $next($request, $response);
    }
}
