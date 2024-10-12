<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\TeamRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TeamController
{
    protected TeamRepository $teamRepository;

    // Inject the repository via constructor
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    // Fetch all teams

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getAllTeams(Request $request, Response $response): Response
    {
        $teams = $this->teamRepository->getAllTeams();
        $response->getBody()->write($teams->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Fetch a team by ID

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     * @throws \JsonException
     */
    public function getTeamById(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $team = $this->teamRepository->getTeamById($id);

        if ($team === null) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Team not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        $response->getBody()->write($team->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Create a new team
    public function createTeam(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $team = $this->teamRepository->createTeam($data);
        $response->getBody()->write(json_encode($team, JSON_THROW_ON_ERROR));
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

    /**
     * @param Request $request
     * @param Response $response
     * @param string[] $args
     * @return Response
     * @throws \JsonException
     */
    public function deleteTeam(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $deleted = $this->teamRepository->deleteTeam($id);

        if (!$deleted) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Team not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        return $response->withStatus(204)->withHeader('Content-Type', 'application/json');
    }
}
