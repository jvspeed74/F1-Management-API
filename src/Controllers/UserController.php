<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AbstractController;
use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends AbstractController
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }
}
