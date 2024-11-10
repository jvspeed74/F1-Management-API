<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class AbstractModel
 * @package App
 * @mixin Model
 * @mixin EloquentBuilder
 * @mixin QueryBuilder
 *
 */
abstract class AbstractModel extends Model {}
