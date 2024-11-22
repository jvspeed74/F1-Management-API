<?php

declare(strict_types=1);

namespace App\Authentication;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuthenticator
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    //    public function createToken(array $data): string
    //    {
    //        $issuedAt = time();
    //        $expirationTime = $issuedAt + 3600; // jwt valid for 1 hour
    //        $payload = array_merge($data, [
    //            'iat' => $issuedAt,
    //            'exp' => $expirationTime,
    //        ]);
    //
    //        return JWT::encode($payload, $this->secretKey, 'HS256');
    //    }

    /**
     * @param string $token
     * @return array<mixed>
     */
    public function validate(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Throwable) {
            return [];
        }
    }
}
