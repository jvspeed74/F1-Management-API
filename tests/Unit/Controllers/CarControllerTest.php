<?php

declare(strict_types=1);

use App\Controllers\CarController;
use App\Models\Car;
use App\Repositories\CarRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

covers(CarController::class);

afterEach(function () {
    Mockery::close();
});

test('get all cars', function () {
    // Mock the CarRepository
    $carRepository = Mockery::mock(CarRepository::class);

    // Mock a collection of cars
    $mockCars = Mockery::mock(EloquentCollection::class);
    $mockCars->shouldReceive('toJson')->andReturn('[]');

    // Define the behavior of getAllCars
    $carRepository->shouldReceive('getAll')
        ->once()
        ->andReturn($mockCars);

    // Create the controller
    $controller = new CarController($carRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->getAll($response);

    // Assert that the response is JSON and the status is 200
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('[]');
});

test('get car by id', function () {
    // Mock the CarRepository
    $carRepository = Mockery::mock(CarRepository::class);

    // Mock a single car
    $mockCar = Mockery::mock(Car::class);
    $mockCar->shouldReceive('toJson')->andReturn('{"id": 1, "make": "Toyota", "model": "Corolla"}');

    // Define the behavior of getCarById
    $carRepository->shouldReceive('getById')
        ->with(1)
        ->once()
        ->andReturn($mockCar);

    // Create the controller
    $controller = new CarController($carRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->getById($response, 1);

    // Assert that the response is JSON and the status is 200
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "make": "Toyota", "model": "Corolla"}');
});

test('create car', function () {
    // Mock the CarRepository
    $carRepository = Mockery::mock(CarRepository::class);

    // Mock the request
    $request = Mockery::mock(Request::class);
    $request->shouldReceive('getParsedBody')->once()->andReturn([
        'make' => 'Toyota',
        'model' => 'Corolla',
    ]);

    // Mock the created car
    $mockCar = Mockery::mock(Car::class);
    $mockCar->shouldReceive('toJson')->andReturn('{"id": 1, "make": "Toyota", "model": "Corolla"}');

    // Define the behavior of createCar
    $carRepository->shouldReceive('create')
        ->with(['make' => 'Toyota', 'model' => 'Corolla'])
        ->once()
        ->andReturn($mockCar);

    // Create the controller
    $controller = new CarController($carRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->create($request, $response);

    // Assert that the response is JSON and the status is 201
    expect($result->getStatusCode())
        ->toBe(201)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "make": "Toyota", "model": "Corolla"}');
});

test('update car', function () {
    // Mock the CarRepository
    $carRepository = Mockery::mock(CarRepository::class);

    // Mock the request
    $request = Mockery::mock(Request::class);
    $request->shouldReceive('getParsedBody')->once()->andReturn([
        'make' => 'Toyota',
        'model' => 'Corolla',
    ]);

    // Mock the updated car
    $mockCar = Mockery::mock(Car::class);
    $mockCar->shouldReceive('toJson')->andReturn('{"id": 1, "make": "Toyota", "model": "Corolla"}');

    // Define the behavior of updateCar
    $carRepository->shouldReceive('update')
        ->with(1, ['make' => 'Toyota', 'model' => 'Corolla'])
        ->once()
        ->andReturn($mockCar);

    // Create the controller
    $controller = new CarController($carRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->update($request, $response, 1);

    // Assert that the response is JSON and the status is 200
    expect($result->getStatusCode())
        ->toBe(200)
        ->and($result->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and((string) $result->getBody())->toBe('{"id": 1, "make": "Toyota", "model": "Corolla"}');
});

test('delete car', function () {
    // Mock the CarRepository
    $carRepository = Mockery::mock(CarRepository::class);

    // Define the behavior of deleteCar
    $carRepository->shouldReceive('delete')
        ->with(1)
        ->once()
        ->andReturn(true);

    // Create the controller
    $controller = new CarController($carRepository);

    // Mock the response
    $response = new Response();

    // Call the controller method
    $result = $controller->delete($response, 1);

    // Assert that the status is 204
    expect($result->getStatusCode())->toBe(204);
});
