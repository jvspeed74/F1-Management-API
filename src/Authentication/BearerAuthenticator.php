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

            $encodedResults = json_encode($results, JSON_PRETTY_PRINT);

            // Check if JSON encoding failed
            if ($encodedResults === false) {
                $encodedResults = json_encode(['status' => 'Failed to encode JSON response'], JSON_PRETTY_PRINT);
            }

            // Write the JSON string to the response body
            $response->getBody()->write($encodedResults);

            // Set the Content-Type to application/json and return 401 status
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        // Retrieve the Authorization header
        $auth = $request->getHeader('Authorization');

        // The value of the Authorization header is in the form "Bearer <token>"
        if (strpos($auth[0], 'Bearer ') !== 0) {
            $results = [
                'status' => 'Invalid Authorization header format'
            ];

            // Encode the results into JSON
            $encodedResults = json_encode($results, JSON_PRETTY_PRINT);

            // Check if JSON encoding failed
            if ($encodedResults === false) {
                // Handle JSON encoding error (fallback response)
                $encodedResults = json_encode(['status' => 'Failed to encode JSON response'], JSON_PRETTY_PRINT);
            }

            // Write the JSON string to the response body
            $response->getBody()->write($encodedResults);

            // Set the Content-Type to application/json and return 400 status
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Extract the token by removing the "Bearer " prefix
        $token = substr($auth[0], 7);

        // Validate the Bearer token using TokenRepository
        $tokenRecord = $this->tokenRepository->validateBearerToken($token);

        // Handle the case where the token is invalid
        if ($tokenRecord === null) {
            $results = [
                'status' => 'Authentication failed'
            ];

            // Encode the results into JSON
            $encodedResults = json_encode($results, JSON_PRETTY_PRINT);

            // Check if JSON encoding failed
            if ($encodedResults === false) {
                // Handle JSON encoding error (fallback response)
                $encodedResults = json_encode(['status' => 'Failed to encode JSON response'], JSON_PRETTY_PRINT);
            }

            // Write the JSON string to the response body
            $response->getBody()->write($encodedResults);

            // Set the Content-Type to application/json and return 401 status
            return $response->withHeader('Content-Type', 'application/json')
                ->withStatus(401);
        }

        // Continue to the next middleware or handler
        return $next($request, $response);
    }
}
