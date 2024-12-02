<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AbstractController;
use App\Models\User;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Psr7\Request;

class UserController extends AbstractController
{
    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
    }

    public function signin(ServerRequestInterface $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        /* @var User $user */
        $user = $this->repository->findBy('username', $username);

        if ($user && password_verify($password, $user->password)) {
            $jwt = JWT::encode(['id' => $user->id, 'username' => $user->username], 'secret', 'HS256');
            $response->getBody()->write(json_encode(['jwt' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    public function signout() {}

    public function signup() {}
}
