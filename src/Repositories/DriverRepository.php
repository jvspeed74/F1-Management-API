<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AbstractModel;
use App\Contracts\AbstractRepository;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

class DriverRepository extends AbstractRepository
{
    /**
     * @param Driver $model
     */
    public function __construct(Driver $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $q
     * @return Collection<int, AbstractModel>
     */
    public function search(string $q): Collection
    {
        $terms = explode(' ', $q);
        return $this->model::where(function ($query) use ($terms) {
            foreach ($terms as $term) {
                $query
                    ->orWhere('first_name', 'LIKE', "%$term%")
                    ->orWhere('last_name', 'LIKE', "%$term%");
            }
        })
            ->get();
    }
}
