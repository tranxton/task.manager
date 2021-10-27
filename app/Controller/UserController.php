<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Shows login form
     *
     * @return Response
     */
    public function getForm(): Response
    {
        if($this->user !== null) {
            return $this->redirect('/');
        }

        $data = [
            'title' => 'Авторизация',
            'errors' => $this->getMessages(),
        ];

        return $this->view('auth.login', $data);
    }

    /**
     * Authorization
     *
     * @return Response
     */
    public function login(): Response
    {
        $login = (string) $this->request->get('login');
        $password = (string) $this->request->get('password');

        if (empty($login) || empty($password)) {
            return $this->redirectWithMessages(
                '/login',
                ['message' => 'Неверный логин или пароль', 'fields' => ['login' => $login]]
            );
        }

        /**
         * @var User $user
         */
        $user = UserRepository::getByLogin($login);
        if ($user === null || !password_verify($password, $user->password)) {
            return $this->redirectWithMessages(
                '/login',
                ['message' => 'Неверный логин или пароль', 'fields' => ['login' => $login]]
            );
        }
        $token = bin2hex(random_bytes(50));
        if (UserRepository::setRememberToken($user, $token)) {
            $cookie = new Cookie('token', $token, (new \DateTime())->add(new \DateInterval('P1D')));

            return $this->redirectWithMessages(
                '/',
                [['message' => 'Вы успешно авторизованы', 'type' => 'success']],
                302,
                [],
                [$cookie]
            );
        }

        return $this->redirectWithMessages(
            '/login',
            ['message' => 'Произошла непредвиденная ошибка. Попробуйте еще раз.', 'fields' => ['login' => $login]]
        );
    }

    /**
     * Logouts user
     *
     * @return Response
     */
    public function logout(): Response
    {
        if ($this->user === null) {
            return $this->redirectWithMessages('/login', ['message' => 'Требуется авторизация']);
        }

        if (!UserRepository::deleteRememberToken($this->user)) {
            return $this->redirectWithMessages('/', [['message' => 'Требуется авторизация', 'type' => 'danger']]);
        }

        $cookie = new Cookie('token', '', 1);

        return $this->redirectWithMessages(
            '/login',
            ['message' => 'Требуется авторизация'],
            302,
            [],
            [$cookie]
        );
    }
}