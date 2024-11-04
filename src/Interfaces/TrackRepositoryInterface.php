<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TrackRepositoryInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return LengthAwarePaginator<int, Track>
     */
    public function getAllTracks(int $page, int $limit, string $sortBy, string $order): LengthAwarePaginator;

    /**
     * @param int $id
     * @return Track|false
     */
    public function getTrackById(int $id): Track | false;

    /**
     * @param array<string, string> $data
     * @return Track
     */
    public function createTrack(array $data): Track;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Track|false
     */
    public function updateTrack(int $id, array $data): Track | false;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteTrack(int $id): bool;
}
