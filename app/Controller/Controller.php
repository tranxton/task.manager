<?php

declare(strict_types=1);

namespace App\Controller;

use App\Kernel;
use App\Model\User;
use App\Repository\UserRepository;
use Jenssegers\Blade\Blade;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    /**
     * @var User|null
     */
    protected $user;

    /**
     * @var Blade
     */
    private $blade;

    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(Kernel $kernel, Blade $blade)
    {
        $this->kernel = $kernel;
        $this->blade = $blade;
        $this->user = $this->getUser();
    }

    /**
     * Returns response
     *
     * @param string|null $content
     * @param int         $status
     * @param array       $headers
     *
     * @return Response
     */
    protected function response(?string $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Returns rendered view
     *
     * @param string $name
     * @param array  $data
     *
     * @return Response
     */
    protected function view(string $name, array $data = []): Response
    {
        return $this->response($this->blade->render($name, $data));
    }

    /**
     * Creates redirect response
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @param array<Cookie> $cookies
     *
     * @return Response
     */
    protected function redirect(string $url, int $status = 302, array $headers = [], array $cookies = []): Response
    {
        $response = new RedirectResponse($url, $status, $headers);
        if ($cookies !== []) {
            foreach ($cookies as $cookie) {
                $response->headers->setCookie($cookie);
            }
        }

        return $response;
    }

    /**
     * Creates redirect response with errors
     *
     * @param string $url
     * @param array  $messages
     * @param int $status
     * @param array $headers
     * @param array<Cookie> $cookies
     *
     * @return Response
     */
    protected function redirectWithMessages(
        string $url,
        array $messages,
        int $status = 302,
        array $headers = [],
        array $cookies = []
    ): Response {
        $bag = $this->kernel->get(Request::class)->getSession()->getFlashBag();
        $bag->setAll($messages);

        return $this->redirect($url, $status, $headers, $cookies);
    }

    /**
     * Returns flashed messages
     *
     * @return array
     */
    protected function getMessages(): array
    {
        /**
         * @var Request $request
         */
        $request = $this->kernel->get(Request::class);

        return $request->getSession()->getFlashBag()->all();
    }

    /**
     * Returns authorized User
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        /**
         * @var Request $request
         */
        $request = $this->kernel->get(Request::class);
        if (($token = $request->cookies->get('token')) === null) {
            return null;
        }

        return UserRepository::getByRememberToken($token);
    }
}