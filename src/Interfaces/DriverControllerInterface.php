<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface DriverControllerInterface
{
    public function getAllDrivers(Response $response): Response;
    public function getDriverById(Response $response, int $id): Response;
    public function createDriver(Request $request, Response $response): Response;
    public function updateDriver(Request $request, Response $response, int $id): Response;
    public function deleteDriver(Response $response, int $id): Response;
}
