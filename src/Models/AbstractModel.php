<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class AbstractModel
 * @package App
 * @mixin EloquentModel
 * @mixin EloquentBuilder
 * @mixin QueryBuilder
 *
 */
abstract class AbstractModel extends Model {}
