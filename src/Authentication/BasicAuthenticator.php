<?php

declare(strict_types=1);

namespace App\Authentication;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;

class BasicAuthenticator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function authenticate(string $username, string $password): ?Model
    {
        /** @var User|null $user */
        $user = $this->userRepository->findBy('username', $username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return null;
    }
}
