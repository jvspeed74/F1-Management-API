<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\AbstractModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Track extends AbstractModel
{
    // Define the table name explicitly if it's not the plural of the model name
    protected $table = 'tracks';

    // Primary key is 'id', and Eloquent will automatically handle it
    protected $primaryKey = 'id';

    // Disable timestamps since the table doesn't have created_at/updated_at columns
    public $timestamps = false;

    // Define the fillable fields for mass assignment
    protected $fillable = ['name', 'length_km', 'continent', 'country_id', 'description'];

    // Cast length_km as float
    /**
     * @var string[]
     */
    protected $casts = [
        'length_km' => 'float',
    ];

    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * Override the save method to enforce non-negative length_km
     */
    protected static function booted(): void
    {
        static::saving(function ($track) {
            if ($track->length_km < 0) {
                throw new InvalidArgumentException('The length of the track must be a non-negative number.');
            }
        });
    }
}
