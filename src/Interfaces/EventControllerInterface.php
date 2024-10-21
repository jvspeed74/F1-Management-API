<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface EventControllerInterface
{
    public function getAllEvents(Response $response): Response;
    public function getEventById(Response $response, int $id): Response;
    public function createEvent(Request $request, Response $response): Response;
    public function updateEvent(Request $request, Response $response, int $id): Response;
    public function deleteEvent(Response $response, int $id): Response;
}
