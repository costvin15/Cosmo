<?php

namespace App\Http\Controller;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 * @package App\Http\Controller
 * @Controller
 */
class HomeController extends AbstractController
{
    /**
     * HomeController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return static
     * @Get(name="/", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="home.index")
     */
    public function indexAction(Request $request, Response $response) {
        $router = $this->_ci->get('router');
        return $response->withRedirect($router->pathFor('login.index'));
    }
}