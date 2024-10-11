<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TeamRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TeamController
{
    protected TeamRepositoryInterface $teamRepository;

    // Inject the repository via constructor
    public function __construct(TeamRepositoryInterface $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    // Fetch all teams
    public function getAllTeams(Request $request, Response $response): Response
    {
        $teams = $this->teamRepository->getAllTeams();
        $response->getBody()->write($teams->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Fetch a team by ID
    public function getTeamById(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $team = $this->teamRepository->getTeamById($id);

        if ($team) {
            $response->getBody()->write(json_encode($team));
        } else {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Team not found"]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Create a new team
    public function createTeam(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $team = $this->teamRepository->createTeam($data);
        $response->getBody()->write(json_encode($team));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // Update a team by ID
    public function updateTeam(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();
        $team = $this->teamRepository->updateTeam($id, $data);

        if ($team) {
            $response->getBody()->write(json_encode($team));
        } else {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Team not found"]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    // Delete a team by ID
    public function deleteTeam(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $deleted = $this->teamRepository->deleteTeam($id);

        if ($deleted) {
            $response->getBody()->write(json_encode(["message" => "Team deleted"]));
        } else {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Team not found"]));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
