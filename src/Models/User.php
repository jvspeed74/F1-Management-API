<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    protected $table = 'users';

    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'password',
        'email',
    ];

    /**
     * Automatically hash the password when setting it.
     */
    public function setPasswordAttribute($password)
    {
        // Hash the password before saving it
        $this->attributes['password'] = Hash::make($password);
    }

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'age' => 'integer',
        'email' => 'string',
    ];
}
