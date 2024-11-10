<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @return Collection<array-key, Model>
     */
    abstract public function getAll(): Collection;

    /**
     * @param int $id
     * @return Model|false
     */
    abstract public function getById(int $id): Model|false;

    /**
     * @param array<string,string> $data
     * @return Model
     */
    abstract public function create(array $data): Model;

    /**
     * @param int $id
     * @param array<string,string> $data
     * @return Model|false
     */
    abstract public function update(int $id, array $data): Model|false;

    /**
     * @param int $id
     * @return bool
     */
    abstract public function delete(int $id): bool;
}
