<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

interface DriverRepositoryInterface
{
    /**
     * @return Collection<int, Driver>
     */
    public function getAllDrivers(): Collection;

    /**
     * @param int $id
     * @return Driver|false
     */
    public function getDriverById(int $id): Driver | false;

    /**
     * @param array<string, string> $data
     * @return Driver
     */
    public function createDriver(array $data): Driver;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Driver|false
     */
    public function updateDriver(int $id, array $data): Driver | false;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteDriver(int $id): bool;
}
