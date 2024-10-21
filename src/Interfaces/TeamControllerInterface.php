<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface TeamControllerInterface
{
    public function getAllTeams(Response $response): Response;
    public function getTeamById(Response $response, int $id): Response;
    public function createTeam(Request $request, Response $response): Response;
    public function updateTeam(Request $request, Response $response, int $id): Response;
    public function deleteTeam(Response $response, int $id): Response;
}
