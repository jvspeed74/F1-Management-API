<?php

declare(strict_types=1);

namespace Tests\Unit\Controllers;

use App\Contracts\AbstractController;
use App\Contracts\AbstractModel;
use App\Contracts\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

covers(AbstractController::class);



beforeEach(function () {
    $this->repository = Mockery::mock(AbstractRepository::class);
    $this->controller = new class ($this->repository) extends AbstractController {
        public function __construct(AbstractRepository $repository)
        {
            parent::__construct($repository);
        }
    };
    $this->response = new Response();
    $this->collection = Mockery::mock(Collection::class);
    $this->model = Mockery::mock(AbstractModel::class);
    $this->mockRequest = Mockery::mock(Request::class);
});

afterEach(function () {
    Mockery::close();
});


describe('getAll', function () {
    it('returns a status code of 200 on success', function () {
        $this->repository
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($this->collection);
        $this->collection
            ->shouldReceive('toJson')
            ->once()
            ->andReturn('[]');

        $result = $this->controller->getAll($this->response);

        expect($result->getStatusCode())
            ->toBe(200)
            ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
            ->and((string) $result->getBody())->toBe('[]');
    });
});

describe('getById', function () {
    it('returns a status code of 200 on success', function () {
        $this->repository->shouldReceive('getById')->with(1)->once()->andReturn($this->model);
        $this->model->shouldReceive('toJson')->once()->andReturn('{"id": 1, "name": "Item A"}');

        $result = $this->controller->getById($this->response, 1);

        expect($result->getStatusCode())
            ->toBe(200)
            ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
            ->and((string) $result->getBody())->toBe('{"id": 1, "name": "Item A"}');
    });

    it('returns a status code of 404 if item not found', function () {
        $this->repository->shouldReceive('getById')->with(1)->once()->andReturn(false);

        $result = $this->controller->getById($this->response, 1);

        expect($result->getStatusCode())
            ->toBe(404)
            ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
            ->and((string) $result->getBody())->toBe('{"message":"Item not found"}');
    });
});

describe('create', function () {

    it('creates an item and returns it back to the user', function () {
        $this->mockRequest->shouldReceive('getParsedBody')->once()->andReturn(['name' => 'Item A']);
        $this->repository->shouldReceive('create')->with(['name' => 'Item A'])->once()->andReturn($this->model);
        $this->model->shouldReceive('toJson')->once()->andReturn('{"id": 1, "name": "Item A"}');

        $result = $this->controller->create($this->mockRequest, $this->response);

        expect($result->getStatusCode())
            ->toBe(201)
            ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
            ->and((string) $result->getBody())->toBe('{"id": 1, "name": "Item A"}');
    });

    it('returns 400 for bad response', function () {
        $this->mockRequest->shouldReceive('getParsedBody')->once()->andReturn(null);

        $result = $this->controller->create($this->mockRequest, $this->response);

        expect($result->getStatusCode())
            ->toBe(400)
            ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
            ->and((string) $result->getBody())->toBe('{"message":"Invalid JSON body"}');
    });
});
