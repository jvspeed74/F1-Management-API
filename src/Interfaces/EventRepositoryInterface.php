<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface
{
    /**
     * @return Collection<int, Event>
     */
    public function getAllEvents(): Collection;

    /**
     * @param int $id
     * @return Event|false
     */
    public function getEventById(int $id): Event | false;

    /**
     * @param array<string, string> $data
     * @return Event
     */
    public function createEvent(array $data): Event;

    /**
     * @param int $id
     * @param array<string, string> $data
     * @return Event|false
     */
    public function updateEvent(int $id, array $data): Event | false;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteEvent(int $id): bool;
}
