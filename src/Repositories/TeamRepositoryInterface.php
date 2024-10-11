<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface TeamRepositoryInterface
{
    public function getAllTeams(): Collection;
    public function getTeamById(int $id);
    public function createTeam(array $data);
    public function updateTeam(int $id, array $data);
    public function deleteTeam(int $id);
}
