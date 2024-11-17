<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Token extends Model
{
    const EXPIRE = 3600;

    protected $table = 'tokens';

    protected $primaryKey = 'id';

    protected $fillable = ['user', 'value'];

    /**
     * Generate or refresh the Bearer token.
     * @param int $id
     * @return string
     */
    public static function generateBearer($id)
    {
        // Find an existing token for the user
        $token = self::where('user', $id)->first();

        if ($token) {
            // Check if the token has expired
            $expiryTime = Carbon::parse($token->updated_at)->addSeconds(self::EXPIRE);
            if ($expiryTime->isPast()) {
                $token->value = bin2hex(random_bytes(64));
                $token->save();
            }
            return $token->value;
        }

        // If no token exists, create a new one
        $token = new Token();
        $token->user = $id;
        $token->value = bin2hex(random_bytes(64));
        $token->save();
        return $token->value;
    }

    /**
     * Validate a Bearer token by matching the token with a database record.
     *
     * @param string $value
     * @return Token|null
     */
    public static function validateBearer($value)
    {
        // Find the token in the database
        $token = self::where('value', $value)->first();

        if ($token) {
            // Check if the token has expired
            $expiryTime = Carbon::parse($token->updated_at)->addSeconds(self::EXPIRE);
            if ($expiryTime->isFuture()) {
                return $token;
            }
        }

        return null;
    }
}
