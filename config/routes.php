<?php

declare(strict_types=1);

use App\Controllers\{CarController, DriverController, EventController, TeamController, TrackController};
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Register application routes.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {
    // Greet route
    $app->get('/hello/{name}', function (Response $response, string $name) {
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    // Team routes
    $app->group('/teams', function (RouteCollectorProxy $group) {
        $group->get('', [TeamController::class, 'getAllTeams']);
        $group->get('/{id:\d+}', [TeamController::class, 'getTeamById']);
        $group->post('', [TeamController::class, 'createTeam']);
        $group->patch('/{id:\d+}', [TeamController::class, 'updateTeam']);
        $group->delete('/{id:\d+}', [TeamController::class, 'deleteTeam']);
    });

    // Event routes
    $app->group('/events', function (RouteCollectorProxy $group) {
        $group->get('', [EventController::class, 'getAllEvents']);
        $group->get('/{id:\d+}', [EventController::class, 'getEventById']);
        $group->post('', [EventController::class, 'createEvent']);
        $group->patch('/{id:\d+}', [EventController::class, 'updateEvent']);
        $group->delete('/{id:\d+}', [EventController::class, 'deleteEvent']);
    });

    // Track routes
    $app->group('/tracks', function (RouteCollectorProxy $group) {
        $group->get('', [TrackController::class, 'getAllTracks']);
        $group->get('/{id:\d+}', [TrackController::class, 'getTrackById']);
        $group->post('', [TrackController::class, 'createTrack']);
        $group->patch('/{id:\d+}', [TrackController::class, 'updateTrack']);
        $group->delete('/{id:\d+}', [TrackController::class, 'deleteTrack']);
    });

    // Driver routes
    $app->group('/drivers', function (RouteCollectorProxy $group) {
        $group->get('', [DriverController::class, 'getAllDrivers']);
        $group->get('/{id:\d+}', [DriverController::class, 'getDriverById']);
        $group->post('', [DriverController::class, 'createDriver']);
        $group->patch('/{id:\d+}', [DriverController::class, 'updateDriver']);
        $group->delete('/{id:\d+}', [DriverController::class, 'deleteDriver']);
        $group->get('/search', [DriverController::class, 'searchDrivers']);
    });

    // Car routes
    $app->group('/cars', function (RouteCollectorProxy $group) {
        $group->get('', [CarController::class, 'getAll']);
        $group->get('/{id:\d+}', [CarController::class, 'getById']);
        $group->post('', [CarController::class, 'create']);
        $group->patch('/{id:\d+}', [CarController::class, 'update']);
        $group->delete('/{id:\d+}', [CarController::class, 'delete']);
    });
};
