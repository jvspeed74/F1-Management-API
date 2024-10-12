<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class TeamRepository
{
    protected Team $model;

    // Inject the Team model via the constructor
    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    // Fetch all teams

    /**
     * @return Collection<int, Team>
     */
    public function getAllTeams(): Collection
    {
        return $this->model::all();  // Uses Eloquent's all() method on the injected model
    }

    // Fetch a team by ID

    /**
     * @param int $id
     * @return Collection<int, Team>|Team|null
     */
    public function getTeamById(int $id): Collection | Model | null
    {
        return $this
            ->model
            ->query()
            ->find($id);
    }

    // Create a new team
    public function createTeam(array $data)
    {
        return $this->model->query()->create($data);
    }

    // Update an existing team
    public function updateTeam($id, array $data)
    {
        $team = $this->model->find($id);  // Find the team by ID
        if ($team) {
            $team->update($data);  // Update the team with the provided data
            return $team;
        }
        return null;  // Return null if the team is not found
    }

    // Delete a team by ID
    public function deleteTeam(int $id): ?bool
    {
        $team = $this->model->query()->find($id);  // Find the team by ID
        if ($team) {
            return $team->delete();
        }
        return false;  // Return false if the team is not found
    }
}
