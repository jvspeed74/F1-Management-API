<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @return Collection<int, AbstractModel>
     */
    public function getAll(): Collection;

    /**
     * @param int $id
     * @return Model|false
     */
    public function getById(int $id): Model | false;

    /**
     * @param array<string, string> $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Model|false
     */
    public function update(int $id, array $data): Model | false;

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    public function getAllWithParams(int $page, int $limit, string $sortBy, string $order): LengthAwarePaginator;

    /**
     * @param string $q
     * @return Collection<int, AbstractModel>
     */
    public function search(string $q): Collection;
}
