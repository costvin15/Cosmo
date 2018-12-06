<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 11/09/17
 * Time: 21:37
 */

namespace App\Http\Controller;

use App\Auth\Adapters\UserWithPassword;
use App\Facilitator\App\SessionFacilitator;
use App\Http\Controller\Validate\LoginControllerValidate;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use SlimAuth\SlimAuthFacade;

/**
 * Class HomeController
 * @package App\Http\Controller
 * @Controller
 * @Route("/login")
 */
class LoginController extends AbstractController
{

    /**
     * LoginController constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        parent::__construct($ci);
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/", alias="login.index")
     */
    public function indexAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/login/login.twig', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/auth", alias="login.auth")
     */
    public function authenticateAction(Request $request, Response $response) {

        if ($request->isXhr()) {
            $validate = new LoginControllerValidate();
            if (!$validate->authenticateAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $session = SessionFacilitator::getSession();

            $authAdapter = new UserWithPassword($request->getParam("username"), md5($request->getParam("password")));
            $auth = new SlimAuthFacade($authAdapter, $session);

            $auth->auth();

            $router = $this->_ci->get('router');
            if ($auth->isValid()) {
                return $response->withJson([ 'callback' => $router->pathFor('dashboard.index') ], 200);
            } else {
                return $response->withJson([ 'Sua conta ou senha está incorreta. Se você não se lembra de sua senha, <a href="' . $router->pathFor('register.password.rescue') . '">redefina-a agora</a>' ], 500);
            }
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/logout", alias="login.logout")
     */
    public function logoutAction(Request $request, Response $response) {
        SessionFacilitator::destroy();

        $router = $this->_ci->get('router');
        return $response->withRedirect($router->pathFor('login.index'));
    }



}