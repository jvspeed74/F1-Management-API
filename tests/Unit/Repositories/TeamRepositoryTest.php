<?php

declare(strict_types=1);

use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

covers(TeamRepository::class);

afterEach(function () {
    Mockery::close();
});

test('get all teams', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Create a mock collection to return
    $mockCollection = Mockery::mock(Collection::class);

    // Define the behavior of the all() method
    $teamMock->shouldReceive('all')
        ->once()
        ->andReturn($mockCollection);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->getAllTeams();

    // Assert that the result matches the mocked collection
    expect($result)->toBe($mockCollection);
});

test('get team by id returns team', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Create a mock of the Team instance that will be returned
    $mockTeam = Mockery::mock(Team::class);

    // Define the behavior of the find() method
    $teamMock->shouldReceive('query->find')
        ->with(1)
        ->once()
        ->andReturn($mockTeam);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->getTeamById(1);

    // Assert that the result matches the mock team
    expect($result)->toBe($mockTeam);
});

test('get team by id returns false if not found', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Define the behavior of the find() method to return null
    $teamMock->shouldReceive('query->find')
        ->with(1)
        ->once()
        ->andReturn(null);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->getTeamById(1);

    // Assert that the result is false
    expect($result)->toBeFalse();
});

test('create team', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Create mock team data
    $mockData = [
        'official_name' => 'Team Test',
        'short_name' => 'TT',
        'headquarters' => 'Test City',
        'team_principal' => 'John Doe',
    ];

    // Create a mock of the created Team instance
    $mockTeam = Mockery::mock(Team::class);

    // Define the behavior of the create() method
    $teamMock->shouldReceive('query->create')
        ->with($mockData)
        ->once()
        ->andReturn($mockTeam);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->createTeam($mockData);

    // Assert that the result matches the mock team
    expect($result)->toBe($mockTeam);
});

test('update team', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Mock data for the update
    $mockData = [
        'official_name' => 'Updated Team',
    ];

    // Create a mock of the Team instance that will be returned
    $mockTeam = Mockery::mock(Team::class);

    // Define the behavior of the find() and update() methods
    $teamMock->shouldReceive('query->find')
        ->with(1)
        ->once()
        ->andReturn($mockTeam);

    $mockTeam->shouldReceive('update')
        ->with($mockData)
        ->once()
        ->andReturn(true);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->updateTeam(1, $mockData);

    // Assert that the result matches the mock team
    expect($result)->toBe($mockTeam);
});

test('delete team', function () {
    // Create a mock of the Team model
    $teamMock = Mockery::mock(Team::class);

    // Create a mock of the Team instance that will be deleted
    $mockTeam = Mockery::mock(Team::class);

    // Define the behavior of the find() and delete() methods
    $teamMock->shouldReceive('query->find')
        ->with(1)
        ->once()
        ->andReturn($mockTeam);

    $mockTeam->shouldReceive('delete')
        ->once()
        ->andReturn(true);

    // Instantiate the repository with the mock
    $teamRepository = new TeamRepository($teamMock);

    // Call the method under test
    $result = $teamRepository->deleteTeam(1);

    // Assert that the result is true
    expect($result)->toBeTrue();
});
