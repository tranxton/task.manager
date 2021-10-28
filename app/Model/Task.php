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
 * @property ?User  $created_by
 * @property ?User  $updated_by
 * @property string $create_at
 * @property string $updated_at
 */
class Task extends Model
{
    public const DEFAULT_SORT = ['field' => 'name', 'type' => 'asc'];

    protected $table = 'tasks';

    protected $perPage = 3;

    protected static $available_sorts_list = [
        'name'   => [
            'name'  => 'Название',
            'types' => [
                'asc'  => 'Возрастанию [А-Я]',
                'desc' => 'Убыванию [Я-А]',
            ],
        ],
        'email'  => [
            'name'  => 'Email',
            'types' => [
                'asc'  => 'Возрастанию [A-Z]',
                'desc' => 'Убыванию [Z-A]',
            ],
        ],
        'is_done' => [
            'name'  => 'Статус',
            'types' => [
                'asc'  => 'Сначала НЕ выполненные',
                'desc' => 'Сначала выполненные',
            ],
        ],
    ];

    /**
     * Returns list of available sorts
     *
     * @return array[]
     */
    public static function getAvailableSortsList(): array
    {
        return self::$available_sorts_list;
    }

    /**
     * Validates sorting
     *
     * @param array<string,string> $sorting
     *
     * @return array
     * @throws \Exception
     */
    public static function validateSorting(array $sorting): array
    {
        $field = $sorting['field'];
        $type = $sorting['type'];
        $field_sort_types = self::$available_sorts_list[$field]['types'] ?? null;
        if ($field_sort_types === null) {
            throw new \Exception("Passed field can't be user in sorting");
        }
        if (!array_key_exists($type, $field_sort_types)) {
            throw new \Exception("Passed sort type can't be user in sorting");
        }

        return [$field, $type];
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'id', 'created_by');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'id', 'updated_by');
    }
}