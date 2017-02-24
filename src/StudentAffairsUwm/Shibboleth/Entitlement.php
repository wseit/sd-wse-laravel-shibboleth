<?php

namespace StudentAffairsUwm\Shibboleth;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Entitlement extends Model
{
    /**
     * Relation to the User model
     * @return Eloquent relation
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model', 'App\User'));
    }

    /**
     * Returns collection of database entitlements found in a string.
     *
     * @param  string $entitlements
     * @return Illuminate\Support\Collection
     */
    public static function findInString($entitlements)
    {
        if (!is_string($entitlements)) {
            $type = gettype($entitlements);
            throw new InvalidArgumentException(
                "Instance of string required, $type given."
            );
        }

        return static::whereIn('name', explode(';', $entitlements))->get();
    }
}
