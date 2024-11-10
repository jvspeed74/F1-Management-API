<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\AbstractModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    protected AbstractModel $model;

    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model::all();
    }

    public function getById(int $id): Model|false
    {
        $record = $this->model->where('id', $id)->first();
        return $record ?: false;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Model|false
    {
        $record = $this->model->find($id);
        if ($record && $record->update($data)) {
            return $record;
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        return $record && $record->delete();
    }
}
