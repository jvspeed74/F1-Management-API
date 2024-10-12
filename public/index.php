<?php

declare(strict_types=1);


use App\Controllers\TeamController;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// TODO Split file into smaller files in the config directory
// TODO Update PHP-DI to version specific to Slim 4
// TODO Add a service for logging
// TODO Add a default error and exception handler

// Create the PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Optionally, add definitions manually if needed (you can skip this for auto-wiring)
// $containerBuilder->addDefinitions([
//     // Manually define services if necessary (like config settings or shared services)
// ]);

$container = $containerBuilder->build();

// Set the container in Slim AppFactory
AppFactory::setContainer($container);

// Set up Eloquent Capsule (Database Connection)
$capsule = new Capsule();

// Add your database connection configuration
$capsule->addConnection([
    'driver'    => 'mysql',                     // Database driver
    'host'      => 'localhost',                 // Database host
    'database'  => 'f1_db',        // Database name
    'username'  => 'root',             // Database username
    'password'  => '',             // Database password
    'charset'   => 'utf8mb4',                      // Charset (usually utf8)
    'collation' => 'utf8mb4_general_ci',           // Collation (usually utf8_unicode_ci)
]);

// Make the Capsule instance available globally via static methods (optional, but recommended)
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();


/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();


/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();


$app->addBodyParsingMiddleware();

/**
 * Add Error Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});
$app->get('/teams', TeamController::class . ':getAllTeams');
$app->get('/teams/{id}', TeamController::class . ':getTeamById');
$app->post('/teams', TeamController::class . ':createTeam');
$app->patch('/teams/{id}', TeamController::class . ':updateTeam');
$app->delete('/teams/{id}', TeamController::class . ':deleteTeam');


// Run app
$app->run();
