<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $email
 * @property string $name
 * @property string $description
 * @property bool   $is_done
 * @property ?User   $created_by
 * @property ?User  $updated_by
 * @property string $create_at
 * @property string $updated_at
 */
class Task extends Model
{
    protected $table = 'tasks';

    protected $perPage = 3;

    public function created_by()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'id', 'updated_by');
    }
}