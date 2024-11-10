<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\AbstractAPIController;
use App\Repositories\DriverRepository;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DriverController extends AbstractAPIController
{
    // Inject the repository via constructor
    public function __construct(DriverRepository $driverRepository)
    {
        $this->repository = $driverRepository;
    }

    // Fetch all drivers

    /**
     * @param Response $response
     * @return Response
     */
    public function getAll(Response $response): Response
    {
        $drivers = $this->repository->getAll();
        $response->getBody()->write($drivers->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Fetch a driver by ID

    /**
     * @param Response $response
     * @param int $id
     * @return Response
     * @throws JsonException
     */
    public function getById(Response $response, int $id): Response
    {

        // Send the ID to the repository to fetch the driver from the database
        $driver = $this->repository->getById($id);

        // If the driver was not found, return a 404 response
        if (!$driver) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Driver not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return the driver as JSON
        $response->getBody()->write($driver->toJson());
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

        // Send the filtered data to the repository to create the driver in the database
        // TODO Filter the data before sending it to the repository
        $driver = $this->repository->create($data);

        // Return the created driver as JSON with a 201 status code
        $response->getBody()->write($driver->toJson());
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    // Update a driver by ID

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

        // Send the filtered data to the repository to update the driver in the database
        // TODO Filter the data before sending it to the repository
        $driver = $this->repository->update($id, $data);

        // If the update was not successful, return a 404 response
        if (!$driver) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Driver not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return the updated driver as JSON
        $response->getBody()->write($driver->toJson());
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
        // Send the ID to the repository to delete the team from the database
        $deleted = $this->repository->delete($id);

        // If the driver was not found, return a 404 response
        if (!$deleted) {
            $response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Driver not found"], JSON_THROW_ON_ERROR));
            return $response;
        }

        // Return a 204 response (no content) if the team was successfully deleted
        return $response->withStatus(204)->withHeader('Content-Type', 'application/json');
    }

    /**
     * @throws JsonException
     */
    public function search(Request $request, Response $response): Response
    {
        $q = $request->getQueryParams()['q'] ?? '';

        if (!$q) {
            $response = $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(["message" => "Missing search query"], JSON_THROW_ON_ERROR));
            return $response;
        }

        $drivers = $this->repository->search($q);

        $response->getBody()->write($drivers->toJson());
        return $response->withHeader('Content-Type', 'application/json');
    }
}
