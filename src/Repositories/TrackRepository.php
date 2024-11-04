<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\TrackRepositoryInterface;
use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrackRepository implements TrackRepositoryInterface
{
    protected Track $model;

    /**
     * @param Track $model
     */
    public function __construct(Track $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return LengthAwarePaginator<int, Track>
     */
    public function getAllTracks(int $page, int $limit, string $sortBy, string $order): LengthAwarePaginator
    {
        $trackSortFields = ['id', 'name', 'length_km', 'continent', 'country_id', 'description'];

        if (!in_array($sortBy, $trackSortFields, true)) {
            throw new \InvalidArgumentException('Invalid sort field: ' . $sortBy);
        }

        if (!in_array($order, ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException('Invalid order: ' . $order);
        }

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

    /**
     * @param int $id
     * @return Track|false
     */
    public function getTrackById(int $id): Track | false
    {
        // Find the track by ID
        $track = $this->model->query()->find($id);

        // Return false if the track is not found
        if (!$track) {
            return false;
        }

        // Return the track if found
        return $track;
    }


    /**
     * @param array<string, string> $data
     * @return Track
     */
    public function createTrack(array $data): Track
    {
        return $this->model->query()->create($data);
    }


    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Track|false
     */
    public function updateTrack(int $id, array $data): Track | false
    {
        $track = $this->model->query()->find($id);  // Find the track by ID
        if (!$track) {
            return false;  // Return false if the track is not found
        }

        // Update the track with the provided data
        if ($track->update($data)) {
            return $track;
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteTrack(int $id): bool
    {
        $track = $this->model->query()->find($id);  // Find the track by ID
        if ($track) {
            return (bool) $track->delete();
        }
        return false;  // Return false if the track is not found
    }
}
