<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

interface TeamRepositoryInterface
{
    /**
     * @return Collection<int, Team>
     */
    public function getAllTeams(): Collection;

    /**
     * @param int $id
     * @return Team|false
     */
    public function getTeamById(int $id): Team | false;

    /**
     * @param array<string, string> $data
     * @return Team
     */
    public function createTeam(array $data): Team;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Team|false
     */
    public function updateTeam(int $id, array $data): Team | false;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteTeam(int $id): bool;
}
