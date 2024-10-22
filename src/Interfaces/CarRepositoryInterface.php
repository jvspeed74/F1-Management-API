<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

interface CarRepositoryInterface
{
    /**
     * @return Collection<int, Car>
     */
    public function getAllCars(): Collection;

    /**
     * @param int $id
     * @return Car|false
     */
    public function getCarById(int $id): Car | false;

    /**
     * @param array<string, string> $data
     * @return Car
     */
    public function createCar(array $data): Car;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Car|false
     */
    public function updateCar(int $id, array $data): Car | false;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCar(int $id): bool;
}
