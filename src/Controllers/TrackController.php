<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AbstractAPIController;
use App\Repositories\TrackRepository;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TrackController extends AbstractAPIController
{
    public function __construct(TrackRepository $trackRepository)
    {
        $this->repository = $trackRepository;
    }

    public function getAll(Response $response): Response
    {
        $tracks = $this->repository->getAll();
        $response->getBody()->write($tracks->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws JsonException
     */
    public function getAllWithParams(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();
        $page = (int) ($queryParams['page'] ?? 1);
        $limit = (int) ($queryParams['limit'] ?? 10);
        $sortBy = $queryParams['sort_by'] ?? 'id';
        $order = $queryParams['order'] ?? 'asc';

        if ($page < 1) {
            $response->getBody()->write(json_encode(['message' => 'Invalid page number. Must be greater than 0.'], JSON_THROW_ON_ERROR));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if ($limit < 1 || $limit > 100) {
            $response->getBody()->write(json_encode(['message' => 'Invalid limit. Must be between 1 and 100.'], JSON_THROW_ON_ERROR));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $trackSortFields = ['id', 'name', 'length_km', 'continent', 'country_id', 'description'];

        if (!in_array($sortBy, $trackSortFields, true)) {
            $response->getBody()->write(json_encode(['message' => 'Invalid sort field: ' . $sortBy], JSON_THROW_ON_ERROR));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!in_array($order, ['asc', 'desc'], true)) {
            $response->getBody()->write(json_encode(['message' => 'Invalid order. Must be "asc" or "desc".'], JSON_THROW_ON_ERROR));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $tracks = $this->repository->getAllWithParams($page, $limit, $sortBy, $order);

        $totalCount = $this->repository->getAll()->count();
        $totalPages = ceil($totalCount / $limit);

        $response->getBody()->write(json_encode($tracks->items(), JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('X-Total-Count', (string) $totalCount)
            ->withHeader('X-Total-Pages', (string) $totalPages)
            ->withHeader('X-Current-Page', (string) $page)
            ->withHeader('X-Items-Per-Page', (string) $limit);
    }

    // Fetch a track by ID

    /**
     * @param Response $response
     * @param int $id
     * @return Response
     * @throws JsonException
     */
    public function getById(Response $response, int $id): Response
    {
        // Send the ID to the repository to fetch the track from the database
        $track = $this->repository->getById($id);

        // If the track was not found, return a 404 response
        if (!$track) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Track not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return the track as JSON
        $response->getBody()->write($track->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws JsonException
     */
    public function create(Request $request, Response $response): Response
    {
        // getParsedBody can return array|object|null based on the Content-Type header
        // Since the content type is application/json, it will return an array OR an object
        // We need to handle both cases by converting the object to an array
        $data = $request->getParsedBody();

        // If the body is not valid JSON, return a 400 response
        if ($data === null) {
            $response = $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Invalid JSON body"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Convert object to array if necessary
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Send the filtered data to the repository to create the track in the database
        // TODO Filter the data before sending it to the repository
        $track = $this->repository->create($data);

        // Return the created track as JSON with a 201 status code
        $response->getBody()->write($track->toJson());
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // Update a track by ID

    /**
     * @param Request $request
     * @param Response $response
     * @param int $id
     * @return Response
     * @throws JsonException
     */
    public function update(Request $request, Response $response, int $id): Response
    {
        // getParsedBody can return array|object|null based on the Content-Type header
        // Since the content type is application/json, it will return an array OR an object
        // We need to handle both cases by converting the object to an array
        $data = $request->getParsedBody();

        // If the body is not valid JSON, return a 400 response
        if ($data === null) {
            $response = $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Invalid JSON body"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Convert object to array if necessary
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Send the filtered data to the repository to update the track in the database
        // TODO Filter the data before sending it to the repository
        $track = $this->repository->update($id, $data);

        // If the update was not successful, return a 404 response
        if (!$track) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Track not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return the updated track as JSON
        $response->getBody()->write($track->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param Response $response
     * @param int $id
     * @return Response
     * @throws JsonException
     */
    public function delete(Response $response, int $id): Response
    {
        // Send the ID to the repository to delete the track from the database
        $deleted = $this->repository->delete($id);

        // If the track was not found, return a 404 response
        if (!$deleted) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Track not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return a 204 response (no content) if the track was successfully deleted
        return $response->withStatus(204)->withHeader('Content-Type', 'application/json');
    }
}
