<?php

declare(strict_types=1);

namespace App\Contracts;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class AbstractController implements ControllerInterface
{
    protected AbstractRepository $repository;

    /**
     * @param Response $response
     * @return Response
     */
    abstract public function getAll(Response $response): Response;

    /**
     * @param Response $response
     * @param int $id
     * @return Response
     */
    abstract public function getById(Response $response, int $id): Response;

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    abstract public function create(Request $request, Response $response): Response;

    /**
     * @param Request $request
     * @param Response $response
     * @param int $id
     * @return Response
     */
    abstract public function update(Request $request, Response $response, int $id): Response;

    /**
     * @param Response $response
     * @param int $id
     * @return Response
     */
    abstract public function delete(Response $response, int $id): Response;
}
