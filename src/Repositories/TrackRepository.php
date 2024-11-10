<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AbstractRepository;
use App\Models\Track;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrackRepository extends AbstractRepository
{
    /**
     * @param Track $model
     */
    public function __construct(Track $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $page, int $limit, string $sortBy, string $order): LengthAwarePaginator
    {
        return $this->model::query()
            ->orderBy($sortBy, $order)
            ->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->model::query()->count();
    }
}
