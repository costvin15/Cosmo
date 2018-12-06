<?php

namespace App\Http\Controller;

use App\Http\Controller\Validate\RegisterControllerValidate;
use App\Mapper\User;
use App\Model\Util\Mail;
use App\Model\Util\Rand;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RegisterController
 * @package App\Http\Controller
 * @Controller
 * @Route("/login/register")
 */
class RegisterController extends AbstractController
{

    /**
     * RegisterController constructor.
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
     * @Get(name="/", alias="register.index")
     */
    public function registerAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/register/register.twig', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/save", alias="register.save")
     */
    public function saveAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $validate = new RegisterControllerValidate();

            if (!$validate->saveAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $user = $this->_dm->getRepository(User::class)->getUserWithUsername($request->getParam('username'));

            if ($user !== null) {
                return $response->withJson([ 'Email já cadastrado!' ], 500);
            }

            $user = new User();
            $user->setUsername($request->getParam('username'));
            $user->setPassword(md5($request->getParam('password')));
            $user->setFullname($request->getParam('fullname'));

            $this->_dm->persist($user);
            $this->_dm->flush();

            Mail::send(
                [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ],
                [ [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ] ],
                "Bem Vindo.",
                "Bem vindo ao Cosmo " . $user->getFullname() . ".<br>Sua senha de acesso:" . $request->getParam('password'));

            $router = $this->_ci->get('router');
            return $response->withJson([ 'message' => 'Bem vindo ao Cosmo ' . $user->getFullname() . '.', 'callback' => $router->pathFor('login.index') ], 200);

        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/search/username", alias="register.search.mail")
     */
    public function searchAction(Request $request, Response $response) {

        if ($request->isXhr()) {
            $validate = new RegisterControllerValidate();

            if (!$validate->searchAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            try {
                $user = $this->_dm->getRepository(User::class)->getUserWithUsername($request->getQueryParam('username'));
            } catch (\Exception $ex) {
                return $response->withJson([ 'Ocorreu um erro ao pesquisar dados!' ], 500);
            }

            if ($user === null) {
                return $response->withJson([ 'mail' => 'avaliable' ], 200);
            }

            return $response->withJson([ 'Email já cadastrado!' ], 500);
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/password/rescue", alias="register.password.rescue")
     */
    public function rescuePasswordAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/register/passwordrescue.twig', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/password/rescue/send", alias="register.password.rescue.send")
     */
    public function sendRescuePasswordAction(Request $request, Response $response) {

        if ($request->isXhr()) {
            $validate = new RegisterControllerValidate();

            if (!$validate->sendRescuePasswordAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $user = $this->_dm->getRepository(User::class)->getUserWithUsername($request->getParam('username'));

            if ($user === null) {
                return $response->withJson([ 'Email não encontrado!' ], 500);
            }

            $newPassword = Rand::characters(6);
            $user->setPassword(md5($newPassword));

            $this->_dm->persist($user);
            $this->_dm->flush();

            Mail::send(
                [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ],
                [ [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ] ],
                "Sua nova senha do cosmo",
                "<h3>Seu padrinho mágico Cosmo lhe envia uma nova senha</h3><br><strong>$newPassword</strong>");

            $router = $this->_ci->get('router');
            return $response->withJson([ 'message' => 'Sua senha foi encaminhada para o email informado.',
                'callback' => $router->pathFor('login.index'),
            ], 200);
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }
    }

}