<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $login
 * @property string $name
 * @property string $password
 * @property string $remember_token
 * @property string $create_at
 * @property string $updated_at
 */
class User extends Model
{
    protected $table = 'users';

    public function tasks()
    {
        return $this->hasMany(Task::class, 'updated_by', 'id');
    }
}