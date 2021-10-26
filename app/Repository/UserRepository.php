<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

class UserRepository
{
    /**
     * Saves remember token
     *
     * @param User   $user
     * @param string $token
     *
     * @return bool
     */
    public static function setRememberToken(User $user, string $token): bool
    {
        $user->remember_token = $token;

        return $user->save();
    }

    /**
     * Saves remember token
     *
     * @param User $user
     *
     * @return bool
     */
    public static function deleteRememberToken(User $user): bool
    {
        $user->remember_token = null;

        return $user->save();
    }

    /**
     * Gets User by remember token
     *
     * @param string $token
     *
     * @return User|null
     */
    public static function getByRememberToken(string $token): ?User
    {
        return User::where('remember_token', $token)->first();
    }

    /**
     * Gets User by login
     *
     * @param string $login
     *
     * @return User|null
     */
    public static function getByLogin(string $login): ?User
    {
        return User::where('login', $login)->first();
    }
}