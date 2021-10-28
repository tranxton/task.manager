<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Task;
use App\Model\User;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Creates task
     *
     * @param User|null $user
     * @param array     $fields
     *
     * @return bool
     */
    public static function create(?User $user, array $fields): bool
    {
        $task = new Task();
        $task->name = $fields['name'];
        $task->email = $fields['email'];
        $task->description = $fields['description'];
        $task->created_by = $user;

        return $task->save();
    }

    /**
     * Updates task's fields
     *
     * @param Task  $task
     * @param User  $user
     * @param array $fields
     *
     * @return bool
     */
    public static function update(Task $task, User $user, array $fields): bool
    {
        $task->description = $fields['description'];
        $task->is_done = $fields['is_done'];
        $task->updated_by = $user;

        return $task->save();
    }

    /**
     * Returns Task
     *
     * @param int $id
     *
     * @return Task|null
     */
    public static function get(int $id): ?Task
    {
        return Task::where('id', $id)->first();
    }

    /**
     * Returns list of tasks by page number
     *
     * @param int   $pagination
     * @param array $sorting
     *
     * @return Collection
     */
    public static function getList(int $pagination, array $sorting): Collection
    {
        $per_page = (new Task())->getPerPage();
        $skip = self::getFirstRowIdByPageNumber($pagination);
        [$sort_by_field, $sort_type] = Task::validateSorting($sorting);

        return Task::select()
            ->orderBy($sort_by_field, $sort_type)
            ->skip($skip)
            ->take($per_page)
            ->get();
    }

    /**
     * Calculates pagination
     *
     * @param int $current_page_number
     *
     * @return int[]|null[]
     */
    public static function getPagination(int $current_page_number): array
    {
        $prev_page_number = $current_page_number - 1;
        $next_page_number = $current_page_number + 1;
        $per_page = (new Task())->getPerPage();

        $rows_num = Task::all()->count();
        $number_of_pages = (int) ceil($rows_num / $per_page);

        return [
            'current' => $current_page_number,
            'prev'    => $prev_page_number >= 1 ? $prev_page_number : null,
            'next'    => $next_page_number <= $number_of_pages ? $next_page_number : null,
        ];
    }

    /**
     * Returns the first task's ID of passed page number
     *
     * @param int $number
     *
     * @return int
     */
    private static function getFirstRowIdByPageNumber(int $number): int
    {
        $per_page = (new Task())->getPerPage();
        $start_from = $number;
        if ($number !== 1) {
            $start_from = $number * $per_page - ($per_page - 1);
        }

        return $start_from - 1;
    }

}