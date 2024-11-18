<?php

declare(strict_types=1);

use App\Controllers\{CarController,
    DriverController,
    EventController,
    TeamController,
    TrackController,
    UserController};
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Authentication\BearerAuthenticator;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Register application routes.
 *
 * @param App $app The Slim application instance.
 *
 * @return void
 */
return function (App $app): void {

    // Root route
    $app->get('/', function ($request, $response, $args) {
        return $response->write('Welcome to Chatter API!');
    });

    // Hello route with proper handling of the 'name' parameter
    $app->get('/hello/{name}', function ($request, $response, $args) {
        $name = isset($args['name']) ? (string)$args['name'] : 'Guest';
        return $response->write("Hello " . $name);
    });

    // User routes (with Bearer token authentication)
    $app->group('/users', function (RouteCollectorProxy $group) {
        $group->get('', UserController::class . ':getAll');
        $group->get('/{id:\d+}', UserController::class . ':getById');
        $group->post('', UserController::class . ':create');
        $group->put('/{id:\d+}', UserController::class . ':update');
        $group->patch('/{id:\d+}', UserController::class . ':update');
        $group->delete('/{id:\d+}', UserController::class . ':delete');
        $group->post('/authBearer', UserController::class . ':authBearer');
    })
        ->add(function ($request, $handler) {
            // Check if BearerAuthenticator class exists
            if (!class_exists(BearerAuthenticator::class)) {
                throw new \RuntimeException("BearerAuthenticator class not found.");
            }
            // Create instance of BearerAuthenticator middleware
            $authenticator = new BearerAuthenticator();
            return $authenticator->process($request, $handler);
        }); // Protecting user routes with Bearer Authenticator

    // Team routes
    $app->group('/teams', function (RouteCollectorProxy $group) {
        $group->get('', TeamController::class . ':getAll');
        $group->get('/{id:\d+}', TeamController::class . ':getById');
        $group->post('', TeamController::class . ':create');
        $group->patch('/{id:\d+}', TeamController::class . ':update');
        $group->delete('/{id:\d+}', TeamController::class . ':delete');
    });

    // Event routes
    $app->group('/events', function (RouteCollectorProxy $group) {
        $group->get('', EventController::class . ':getAll');
        $group->get('/{id:\d+}', EventController::class . ':getById');
        $group->post('', EventController::class . ':create');
        $group->patch('/{id:\d+}', EventController::class . ':update');
        $group->delete('/{id:\d+}', EventController::class . ':delete');
    });

    // Track routes
    $app->group('/tracks', function (RouteCollectorProxy $group) {
        $group->get('', TrackController::class . ':getAllWithParams');
        $group->get('/{id:\d+}', TrackController::class . ':getById');
        $group->post('', TrackController::class . ':create');
        $group->patch('/{id:\d+}', TrackController::class . ':update');
        $group->delete('/{id:\d+}', TrackController::class . ':delete');
    });

    // Driver routes
    $app->group('/drivers', function (RouteCollectorProxy $group) {
        $group->get('', DriverController::class . ':getAll');
        $group->get('/{id:\d+}', DriverController::class . ':getById');
        $group->post('', DriverController::class . ':create');
        $group->patch('/{id:\d+}', DriverController::class . ':update');
        $group->delete('/{id:\d+}', DriverController::class . ':delete');
        $group->get('/search', DriverController::class . ':search');
    });

    // Car routes
    $app->group('/cars', function (RouteCollectorProxy $group) {
        $group->get('', CarController::class . ':getAll');
        $group->get('/{id:\d+}', CarController::class . ':getById');
        $group->post('', CarController::class . ':create');
        $group->patch('/{id:\d+}', CarController::class . ':update');
        $group->delete('/{id:\d+}', CarController::class . ':delete');
    });

    // Run the application
    $app->run();
};
