<?php

/**
 * Author: Evan Deal
 * Date: 11-17-24
 * File: UserRepository.php
 * Description:
 */
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\AbstractRepository;
use App\Models\User;

class UserRepository extends AbstractRepository
{
    /**
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
