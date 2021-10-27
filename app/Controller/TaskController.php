<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TaskRepository;
use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Form for task creating/editing
     *
     * @return Response
     */
    public function showForm(): Response
    {
        $id = (int) $this->request->get('id');
        $task = null;
        if ($id !== 0) {
            $task = TaskRepository::get($id);
            if ($task === null) {
                return $this->redirectWithMessages('/', [['message' => 'Задача не найдена', 'type' => 'danger']]);
            }
            if ($this->user === null) {
                return $this->redirectWithMessages('/login', ['message' => 'Требуется авторизация']);
            }
            if ($task->is_done) {
                return $this->redirectWithMessages(
                    "/",
                    [['message' => 'После завершения задачу редактировать нельзя', 'type' => 'danger']]
                );
            }
        }

        $data = [
            'user'   => $this->user,
            'task'   => $task,
            'errors' => $this->getMessages(),
        ];
        if (isset($task)) {
            $data['title'] = 'Редактирование задачи';
            $data['button'] = 'Готово';
            $data['url'] = "/task/edit";
        } else {
            $data['title'] = 'Создание задачи';
            $data['button'] = 'Создать';
            $data['url'] = '/task/create';
        }

        return $this->view('task.form', $data);
    }

    /**
     * Task creating
     *
     * @return Response
     */
    public function create(): Response
    {
        $fields = [
            'name'        => (string) $this->request->get('name'),
            'email'       => (string) $this->request->get('email'),
            'description' => (string) $this->request->get('description'),
        ];
        $rules = [
            'name'        => 'required|min:1|max:30',
            'email'       => 'required|min:1|max:30',
            'description' => 'required|min:1|max:255',
        ];
        $validator = (new Validator())->validate($fields, $rules);
        $errors = $validator->errors();
        if ($errors->count() > 0) {
            return $this->redirectWithMessages('/task', $errors->toArray());
        }

        if (!TaskRepository::create($this->user, $fields)) {
            return $this->redirectWithMessages(
                '/task',
                ['message' => 'Не удалось создать задачу. Попробуйте еще раз.']
            );
        }

        return $this->redirectWithMessages('/', [['message' => 'Задача успешно создана', 'type' => 'success']]);
    }

    /**
     * Task editing
     *
     * @return Response
     */
    public function edit(): Response
    {
        if ($this->user === null) {
            return $this->redirectWithMessages('/login', ['message' => 'Требуется авторизация']);
        }
        $fields = [
            'id'          => (int) $this->request->get('id'),
            'description' => (string) $this->request->get('description'),
            'is_done'     => (bool) $this->request->get('is_done'),
        ];
        $rules = [
            'id'          => 'required|integer|min:1',
            'description' => 'required|min:1|max:255',
            'is_done'     => 'nullable|boolean',
        ];
        $validator = (new Validator())->validate($fields, $rules);
        $errors = $validator->errors();
        if ($errors->count() > 0) {
            return $this->redirectWithMessages("/task?id={$fields['id']}", $errors->toArray());
        }

        $task = TaskRepository::get($fields['id']);
        if ($task === null) {
            return $this->redirectWithMessages("/task?id={$fields['id']}", ['message' => 'Задача не найдена']);
        }

        if ($task->is_done) {
            return $this->redirectWithMessages(
                "/",
                [['message' => 'После завершения задачу редактировать нельзя', 'type' => 'danger']]
            );
        }

        if (!TaskRepository::update($task, $this->user, $fields)) {
            return $this->redirectWithMessages(
                "/task?id={$fields['id']}",
                ['message' => 'Не удалось отредактировать задачу. Попробуйте еще раз.']
            );
        }

        return $this->redirectWithMessages('/', [['message' => 'Задача успешно отредактирована', 'type' => 'success']]);
    }

    /**
     * Get list of tasks
     *
     * @return Response
     * @throws \Exception
     */
    public function list(): Response
    {
        $current_page = $this->getCurrentPageNumber();
        $data = [
            'title'      => 'Список задач',
            'user'       => $this->user,
            'messages'   => $this->getMessages(),
            'tasks'      => TaskRepository::getListByPageNumber($current_page),
            'pagination' => TaskRepository::getPagination($current_page),
        ];

        return $this->view('task.list', $data);
    }

    /**
     * Returns current page number
     *
     * @return int
     * @throws \Exception
     */
    private function getCurrentPageNumber(): int
    {
        $current_page = $this->request->get('page') ?? 1;
        if (!is_numeric($current_page)) {
            throw new \Exception('Page must be a number');
        }
        if ((int) $current_page <= 0) {
            throw new \Exception('Page must be more than 0');
        }

        return (int) $current_page;
    }
}