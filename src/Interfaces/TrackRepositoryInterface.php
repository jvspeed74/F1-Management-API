<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;

interface TrackRepositoryInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return Collection<int, Track>
     */
    public function getAllTracks(int $page, int $limit, string $sortBy, string $order): Collection;

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
