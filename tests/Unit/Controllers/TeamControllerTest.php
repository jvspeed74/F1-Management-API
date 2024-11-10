<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use App\Controllers\TeamController;
use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

covers(TeamController::class);

afterEach(function () {
    Mockery::close();
});

test('get all teams', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Mock a collection of teams
    $mockTeams = Mockery::mock(Collection::class);
    $mockTeams->shouldReceive('toJson')->andReturn('[]');

    // Define the behavior of getAllTeams
    $teamRepository->shouldReceive('getAllTeams')
        ->once()
        ->andReturn($mockTeams);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->getAll($response);

    // Assert that the response is JSON and the status is 200
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('[]');
});
test('get team by id returns team', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Mock a single team
    $mockTeam = Mockery::mock(Team::class);
    $mockTeam->shouldReceive('toJson')->andReturn('{"id": 1, "official_name": "Team A"}');

    // Define the behavior of getTeamById
    $teamRepository->shouldReceive('getTeamById')
        ->with(1)
        ->once()
        ->andReturn($mockTeam);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $response = new Response();

    // Call the controller method
    $result = $controller->getById($response, 1);

    // Assert that the response is JSON and the status is 200
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "official_name": "Team A"}');
});

test('get team by id returns 404 if not found', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Define the behavior of getTeamById to return false
    $teamRepository->shouldReceive('getTeamById')
        ->with(1)
        ->once()
        ->andReturn(false);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $response = new Response();

    // Call the controller method
    $result = $controller->getById($response, 1);

    // Assert that the response is JSON, the status is 404, and the correct message is returned
    expect($result->getStatusCode())
        ->toBe(404)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"message":"Team not found"}');
});

test('create team', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Mock a new team
    $mockTeam = Mockery::mock(Team::class);
    $mockTeam->shouldReceive('toJson')->andReturn('{"id": 1, "official_name": "Team A"}');

    // Define the behavior of createTeam
    $teamRepository->shouldReceive('createTeam')
        ->once()
        ->andReturn($mockTeam);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $request = Mockery::mock(ServerRequestInterface::class);
    $response = new Response();

    // Mock the request body
    $request->shouldReceive('getParsedBody')
        ->once()
        ->andReturn(['official_name' => 'Team A']);

    // Call the controller method
    $result = $controller->create($request, $response);

    // Assert that the response is JSON, the status is 201, and the correct team is returned
    expect($result->getStatusCode())
        ->toBe(201)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "official_name": "Team A"}');
});

test('update team returns updated team', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Mock an updated team
    $mockTeam = Mockery::mock(Team::class);
    $mockTeam->shouldReceive('toJson')->andReturn('{"id": 1, "official_name": "Updated Team"}');

    // Define the behavior of updateTeam
    $teamRepository->shouldReceive('updateTeam')
        ->with(1, ['official_name' => 'Updated Team'])
        ->once()
        ->andReturn($mockTeam);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $request = Mockery::mock(ServerRequestInterface::class);
    $response = new Response();

    // Mock the request body
    $request->shouldReceive('getParsedBody')
        ->once()
        ->andReturn(['official_name' => 'Updated Team']);

    // Call the controller method
    $result = $controller->update($request, $response, 1);

    // Assert that the response is JSON, the status is 200, and the updated team is returned
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "official_name": "Updated Team"}');
});

test('delete team returns 204 on success', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Define the behavior of deleteTeam to return true
    $teamRepository->shouldReceive('deleteTeam')
        ->with(1)
        ->once()
        ->andReturn(true);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $request = Mockery::mock(ServerRequestInterface::class);
    $response = new Response();

    // Call the controller method
    $result = $controller->delete($response, 1);

    // Assert that the response status is 204 (No Content)
    expect($result->getStatusCode())->toBe(204);
});
test('delete team returns 404 if not found', function () {
    // Mock the TeamRepository
    $teamRepository = Mockery::mock(TeamRepository::class);

    // Define the behavior of deleteTeam to return false
    $teamRepository->shouldReceive('deleteTeam')
        ->with(1)
        ->once()
        ->andReturn(false);

    // Create the controller
    $controller = new TeamController($teamRepository);

    // Mock the request and response
    $response = new Response();

    // Call the controller method
    $result = $controller->delete($response, 1);

    // Assert that the response is JSON, the status is 404, and the correct message is returned
    expect($result->getStatusCode())
        ->toBe(404)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"message":"Team not found"}');
});
