<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return [
    // Define Monolog logger as a service
    LoggerInterface::class => function () {
        $logger = new Logger('app');
        $fileHandler = new StreamHandler(__DIR__ . '/../logs/app.log', Level::Debug);
        $fileHandler->setFormatter(new Monolog\Formatter\LineFormatter(null, null, true, true));
        $logger->pushHandler($fileHandler);

        return $logger;
    },
    'db' => function () {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection(
            [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'f1_db',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_general_ci',
            ],
        );
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        return $capsule;
    },
];
