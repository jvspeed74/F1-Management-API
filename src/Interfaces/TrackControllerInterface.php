<?php

declare(strict_types=1);

namespace App\Interfaces;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface TrackControllerInterface
{
    public function getAllTracks(Response $response): Response;
    public function getTrackById(Response $response, int $id): Response;
    public function createTrack(Request $request, Response $response): Response;
    public function updateTrack(Request $request, Response $response, int $id): Response;
    public function deleteTrack(Response $response, int $id): Response;
}
