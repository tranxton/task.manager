<?php

declare(strict_types=1);

namespace App;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager;
use Jenssegers\Blade\Blade;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class Kernel extends Container
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Root path
     *
     * @var string
     */
    protected $base_path;

    public function __construct(string $base_path, array $config)
    {
        $this->base_path = $base_path;
        $this->config = $config;
    }

    /**
     * Bootstraps application
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $this->createDbConnection();

        $request = Request::createFromGlobals();
        $this->startSession($request);
        $this->registerSharedInstances($request);
        $this->sendRequestThrowRouter($request);
    }

    /**
     * Creates and setups database connection
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function createDbConnection(): void
    {
        /**
         * @var Manager $capluse
         */
        $capluse = $this->make(Manager::class, [$this]);
        $capluse->addConnection($this->config['database']);
        $capluse->bootEloquent();
    }

    /**
     * Starts session
     *
     * @param Request $request
     */
    private function startSession(Request $request)
    {
        $session = new Session(new NativeSessionStorage(), new AttributeBag(), new FlashBag());

        $session->start();

        $request->setSession($session);
    }

    /**
     * Registers shared instances
     *
     * @param Request $request
     */
    private function registerSharedInstances(Request $request): void
    {
        $this->instance(Request::class, $request);
        $this->instance(Blade::class, $this->createBladeTemplateEngine());
        $this->instance(Kernel::class, $this);
    }

    /**
     * Returns instance of Blade Template Engine
     *
     * @return Blade
     */
    private function createBladeTemplateEngine(): Blade
    {
        $blade = new Blade("{$this->base_path}/views", "{$this->base_path}/cache/views", $this);
        array_map('unlink', array_filter((array) glob("{$this->base_path}/cache/views/*")));

        return $blade;
    }

    /**
     * Handles request using WEB router
     *
     * @param Request $request
     *
     * @return Response
     */
    private function sendRequestThrowRouter(Request $request): Response
    {
        $router = new Router(
            new YamlFileLoader(new FileLocator($this->base_path)),
            "routes.yml",
            ['cache_dir' => $this->base_path . '/cache/routes']
        );
        $router->setContext((new RequestContext())->fromRequest($request));

        try {
            $parameters = $router->matchRequest($request);
            if (isset($parameters['_controller'])) {
                $response = $this->call($parameters['_controller'], [], $parameters['_action']);
            } else {
                $blade = $this->get(Blade::class);
                $response = (new Response($blade->render($parameters['_view'])));
            }

            return $response->send();
        } catch (\Exception $e) {
            if ($e instanceof ResourceNotFoundException) {
                return (new RedirectResponse($router->getGenerator()->generate('404')))->send();
            }

            if ($e instanceof MethodNotAllowedException) {
                return (new RedirectResponse($router->getGenerator()->generate('405')))->send();
            }

            return (new RedirectResponse($router->getGenerator()->generate('500')))->send();
        }
    }
}
