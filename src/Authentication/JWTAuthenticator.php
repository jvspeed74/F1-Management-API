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
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->secretKey = getenv('JWT_SECRET_KEY') ?: 'secret';
        $this->logger = $logger;
    }

    /**
     * Validate a JWT token, receiving the claims if valid or an error message if not.
     *
     * @param string $token The JWT token to validate.
     * @return array<mixed> The claims if the token is valid, or an error message if not.
     */
    public function validate(string $token): array
    {
        try {
            $this->logger->debug('Validating token:', ['token' => $token]);
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (ExpiredException $e) {
            $this->logger->debug('Token expired', ['exception' => $e]);
            return ['error' => 'Token expired'];
        } catch (SignatureInvalidException $e) {
            $this->logger->debug('Invalid token signature', ['exception' => $e]);
            return ['error' => 'Invalid token signature'];
        } catch (BeforeValidException $e) {
            $this->logger->debug('Token not valid yet', ['exception' => $e]);
            return ['error' => 'Token not valid yet'];
        } catch (\Exception $e) {
            $this->logger->error('Caught an unexpected exception while validating', ['exception' => $e]);
            return ['error' => 'Invalid token'];
        }
        return (array) $decoded;
    }

    /**
     * @param array<mixed> $claims
     * @param int $secondsUntilExpiration
     * @return array{token: string, exp: int}
     */
    public function generate(array $claims = [], int $secondsUntilExpiration = 3600): array
    {
        $issuedAt = time();
        $expiresAt = $issuedAt + $secondsUntilExpiration;
        $payload = array_merge($claims, [
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ]);

        $token = JWT::encode($payload, $this->secretKey, 'HS256');
        return [
            'token' => $token,
            'exp' => $expiresAt
        ];
    }
}
