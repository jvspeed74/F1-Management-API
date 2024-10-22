<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface CarControllerInterface
{
    public function getAllCars(Response $response): Response;
    public function getCarById(Response $response, int $id): Response;
    public function createCar(Request $request, Response $response): Response;
    public function updateCar(Request $request, Response $response, int $id): Response;
    public function deleteCar(Response $response, int $id): Response;
}
