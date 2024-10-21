<?php

declare(strict_types=1);

use App\Controllers\{CarController, DriverController, EventController, TeamController, TrackController};
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;

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
        $group->get('', TeamController::class . ':getAllTeams');
        $group->get('/{id}', TeamController::class . ':getTeamById');
        $group->post('', TeamController::class . ':createTeam');
        $group->patch('/{id}', TeamController::class . ':updateTeam');
        $group->delete('/{id}', TeamController::class . ':deleteTeam');
    });

    // Event routes
    $app->group('/events', function (RouteCollectorProxy $group) {
        $group->get('', EventController::class . ':getAllEvents');
        $group->get('/{id}', EventController::class . ':getEventById');
        $group->post('', EventController::class . ':createEvent');
        $group->patch('/{id}', EventController::class . ':updateEvent');
        $group->delete('/{id}', EventController::class . ':deleteEvent');
    });

    // Track routes
    $app->group('/tracks', function (RouteCollectorProxy $group) {
        $group->get('', TrackController::class . ':getAllTracks');
        $group->get('/{id}', TrackController::class . ':getTrackById');
        $group->post('', TrackController::class . ':createTrack');
        $group->patch('/{id}', TrackController::class . ':updateTrack');
        $group->delete('/{id}', TrackController::class . ':deleteTrack');
    });

    // Driver routes
    $app->group('/drivers', function (RouteCollectorProxy $group) {
        $group->get('', DriverController::class . ':getAllDrivers');
        $group->get('/{id}', DriverController::class . ':getDriverById');
        $group->post('', DriverController::class . ':createDriver');
        $group->patch('/{id}', DriverController::class . ':updateDriver');
        $group->delete('/{id}', DriverController::class . ':deleteDriver');
    });

    // Car routes
    $app->group('/cars', function (RouteCollectorProxy $group) {
        $group->get('', CarController::class . ':getAllCars');
        $group->get('/{id}', CarController::class . ':getCarById');
        $group->post('', CarController::class . ':createCar');
        $group->patch('/{id}', CarController::class . ':updateCar');
        $group->delete('/{id}', CarController::class . ':deleteCar');
    });
};
