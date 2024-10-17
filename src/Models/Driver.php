<?php

declare(strict_types=1);

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Driver extends Model
{
    // Define the table name explicitly if it's not the plural of the model name
    protected $table = 'drivers';

    // Primary key is 'id', and Eloquent will automatically handle it
    protected $primaryKey = 'id';

    // Disable timestamps since the table doesn't have created_at/updated_at columns
    public $timestamps = false;

    // Define the fillable fields for mass assignment
    protected $fillable = [
        'first_name',
        'last_name',
        'team_id',
        'nationality_id',
        'birthday',
        'driver_number',
        'career_points',
        'career_wins',
        'career_podiums',
        'championships',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<Nationality, $this>
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }
}

