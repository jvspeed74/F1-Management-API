<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Models\Token;
use App\Repositories\TokenRepository;

class BearerAuthenticator
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';
    private TokenRepository $tokenRepository;

    public function __construct(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    public function validate(string $token): bool
    {
        /** @var Token|null $model */
        $model = $this->tokenRepository->findBy('token', $token);
        if ($model
            && $model->token_type === 'bearer'
            && $model->expires_at > date(self::DATE_FORMAT)
        ) {
            return true;
        }
        return false;
    }
}
