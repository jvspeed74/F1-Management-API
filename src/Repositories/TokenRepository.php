<?php

namespace App\Repositories;

use App\Models\Token;

class TokenRepository
{
    /**
     * Generate or refresh a Bearer Token for the user.
     *
     * @param int $userId
     * @return string
     */
    public function generateBearerToken(int $userId): string
    {
        // Call the Token model's generateBearer method
        return Token::generateBearer($userId);
    }

    /**
     * Validate a Bearer Token.
     *
     * @param string $tokenValue
     * @return Token|null
     */
    public function validateBearerToken(string $tokenValue): ?Token
    {
        // Validate the token using Token model
        return Token::validateBearer($tokenValue);
    }
}
