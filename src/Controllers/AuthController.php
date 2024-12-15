<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Authentication\JWTAuthenticator;
use App\Models\User;
use App\Repositories\TokenRepository;
use App\Repositories\UserRepository;
use DateTime;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class AuthController
{
    private UserRepository $userRepository;
    private JWTAuthenticator $jwtAuthenticator;
    private TokenRepository $tokenRepository;
    private LoggerInterface $logger;

    public function __construct(
        UserRepository $userRepository,
        JWTAuthenticator $jwtAuthenticator,
        TokenRepository $tokenRepository,
        LoggerInterface $logger,
    ) {
        $this->userRepository = $userRepository;
        $this->jwtAuthenticator = $jwtAuthenticator;
        $this->tokenRepository = $tokenRepository;
        $this->logger = $logger;
    }

    /**
     * @throws \JsonException
     */
    public function login(Request $request, Response $response): Response
    {
        $this->logger->info('Handling login request');
        /** @var string[] $data */
        $data = json_decode(
            (string)$request->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $username = $data['username'] ?? null;
        if (!$username) {
            throw new HttpBadRequestException($request, 'Username is required');
        }

        /** @var User|null $user */
        $user = $this->userRepository->findBy('username', $username);
        if (!$user || !password_verify($data['password'], $user->password)) {
            throw new HttpBadRequestException($request, 'Invalid credentials');
        }

        $this->logger->info('User authenticated. Generating token');
        $tokenData = $this->jwtAuthenticator->generate(
            ['user_id' => $user->id, 'username' => $user->username],
        );
        $this->tokenRepository->create([
            'user_id' => $user->id,
            'token' => $tokenData['token'],
            'token_type' => 'jwt',
            'revoked' => false,
            'expires_at' => (new DateTime())->setTimestamp($tokenData['exp']),
        ]);

        $response->getBody()->write(
            json_encode(['token' => $tokenData['token']], JSON_THROW_ON_ERROR),
        );
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws \JsonException
     */
    public function register(Request $request, Response $response): Response
    {
        $this->logger->info('Handling register request');
        /** @var array{username: string|null, password: string|null} $data */
        $data = json_decode(
            (string)$request->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
        $username = isset($data['username']) ? (string)$data['username'] : null;
        if (!$username) {
            throw new HttpBadRequestException($request, 'Username is required');
        }

        $password = isset($data['password']) ? (string)$data['password'] : null;
        if (!$password) {
            throw new HttpBadRequestException($request, 'Password is required');
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if (!$hashedPassword) {
            throw new HttpInternalServerErrorException(
                $request,
                'Failed to hash password',
            );
        }

        if ($this->userRepository->findBy('username', $data['username'])) {
            $this->logger->warning(
                'Username already exists',
                ['username' => $data['username']],
            );
            $response->getBody()->write(
                json_encode(['error' => 'Username already exists'],
                    JSON_THROW_ON_ERROR),
            );
            return $response->withStatus(400)->withHeader(
                'Content-Type',
                'application/json',
            );
        }

        /** @var User $user */
        $user = $this->userRepository->create([
            'username' => $data['username'],
            'password' => $hashedPassword,
        ]);

        $this->logger->info(
            'User registered successfully',
            ['username' => $user->username],
        );
        $response->getBody()->write(
            json_encode(['message' => 'User registered successfully'],
                JSON_THROW_ON_ERROR),
        );
        return $response->withHeader('Content-Type', 'application/json')->withStatus(
            201,
        );
    }

    /**
     * @throws \JsonException
     */
    public function revoke(Request $request, Response $response): Response
    {
        $this->logger->info('Handling revoke request');
        $authHeader = $request->getHeader('Authorization')[0] ?? null;
        if ($authHeader === null) {
            $this->logger->warning('Authorization header missing');
            $response->getBody()->write(
                json_encode(['error' => 'Authorization header missing'],
                    JSON_THROW_ON_ERROR),
            );
            return $response->withStatus(400)->withHeader(
                'Content-Type',
                'application/json',
            );
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->logger->warning('Invalid Authorization header format');
            $response->getBody()->write(
                json_encode(['error' => 'Invalid Authorization header format'],
                    JSON_THROW_ON_ERROR),
            );
            return $response->withStatus(400)->withHeader(
                'Content-Type',
                'application/json',
            );
        }

        $token = $matches[1];
        if ($this->tokenRepository->revoke($token) == 0) {
            $this->logger->error('Failed to revoke token', ['token' => $token]);
            $response->getBody()->write(
                json_encode(['error' => 'Failed to revoke token'],
                    JSON_THROW_ON_ERROR),
            );
            return $response->withStatus(500)->withHeader(
                'Content-Type',
                'application/json',
            );
        }

        $this->logger->info('Revoke request successful');
        $response->getBody()->write(
            json_encode(['message' => 'Token revocation successful'],
                JSON_THROW_ON_ERROR),
        );
        return $response->withHeader('Content-Type', 'application/json');
    }
}
