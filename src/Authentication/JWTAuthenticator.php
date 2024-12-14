<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Models\Token;
use App\Repositories\TokenRepository;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Psr\Log\LoggerInterface;

class JWTAuthenticator
{
    private string $secretKey;
    private TokenRepository $tokenRepository;
    private LoggerInterface $logger;

    public function __construct(TokenRepository $tokenRepository, LoggerInterface $logger)
    {
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'default_secret';
        $this->tokenRepository = $tokenRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $token
     * @return array<mixed>
     */
    public function validate(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            $this->logger->warning('Token expired', ['exception' => $e]);
            return ['error' => 'Token expired'];
        } catch (SignatureInvalidException $e) {
            $this->logger->warning('Invalid token signature', ['exception' => $e]);
            return ['error' => 'Invalid token signature'];
        } catch (BeforeValidException $e) {
            $this->logger->warning('Token not valid yet', ['exception' => $e]);
            return ['error' => 'Token not valid yet'];
        } catch (\Exception $e) {
            $this->logger->error('Invalid token', ['exception' => $e]);
            return ['error' => 'Invalid token'];
        }
    }

    /**
     * @param array<mixed> $claims
     * @return string
     */
    public function generate(array $claims = []): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // jwt valid for 1 hour
        $payload = array_merge($claims, [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
        ]);

        $jwtToken = JWT::encode($payload, $this->secretKey, 'HS256');
        $this->tokenRepository->create([
            'token' => $jwtToken,
            'token_type' => 'jwt',
            'expires_at' => date('Y-m-d H:i:s', $expirationTime),
        ]);

        return $jwtToken;
    }

    public function revoke(string $token): void
    {
        /** @var Token|null $tokenModel */
        $tokenModel = $this->tokenRepository->findBy('token', $token);
        if ($tokenModel === null) {
            return;
        }

        // Implement token revocation logic, e.g., adding to a blacklist
        if ($this->tokenRepository->delete($tokenModel->id)) {
            $this->logger->info('Token revoked', ['token' => $token]);
        } else {
            $this->logger->error('Failed to revoke token', ['token' => $token]);
        }
    }
}
