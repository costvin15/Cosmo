<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 16/09/17
 * Time: 07:40
 */

namespace App\Http\Controller;

use App\Http\Controller\Validate\AdministratorControlValidate;
use App\Mapper\User;
use App\Model\Util\ImageBase64;
use App\Model\Util\Mail;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdministratorControlController
 * @package App\Http\Controller
 * @Controller
 * @Route("/admin")
 */
class AdministratorControlController extends AbstractController
{

    /**
     * AdministratorControlController constructor.
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
     * @Get(name="/", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.index")
     */
    public function indexAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/administratorcontrol/index.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users")
     */
    public function usersAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/administratorcontrol/users.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user/all", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users.all")
     */
    public function findAllUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $router = $this->_ci->get('router');
            $userCollection = $this->_dm->getRepository(User::class)->findAll();

            $arrayUser = [];
            foreach ($userCollection as $user) {
                $arrayUserTemp = $user->toArray();
                $arrayUserTemp['avatar'] = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() .
                    $router->pathFor('photo.get.user', [ 'id' => $arrayUserTemp['_id']]);

                $arrayUser[] = $arrayUserTemp;
            }

            return $response->withJson($arrayUser, 200);
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user/create", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users.new")
     */
    public function newUserAction(Request $request, Response $response) {
        $this->setAttributeView('show_adm', true);
        $this->setAttributeView('formCreate', true);
        return $this->view->render($response, 'View/administratorcontrol/form-user.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @throws \Exception
     * @return mixed
     * @Get(name="/user/modify/{id}", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users.new")
     */
    public function modifyUserAction(Request $request, Response $response, array $args) {
        $this->setAttributeView('show_adm', true);
        $this->setAttributeView('formUpdate', true);

        $id = $args['id'];
        $user = $this->_dm->getRepository(User::class)->find($id);

        if ($user == null)
            throw new \Exception('Usuário não encontrado');

        $image64 = new ImageBase64();
        $pathStorageImage = $this->_ci->get('settings')->get('storage.photo');

        $fileAvatar = '';
        if (!file_exists($pathStorageImage . DIRECTORY_SEPARATOR . $user->getId())) {
            $fileAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . 'default.png';
        } else {
            $fileAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . $user->getId();
        }

        $avatarBase64 = $image64->castPathFile($fileAvatar);

        $this->setAttributeView('user', $user);
        $this->setAttributeView('avatar', $avatarBase64);
        $this->setAttributeView('id', $id);
        return $this->view->render($response, 'View/administratorcontrol/form-user.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/user/save", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users.save")
     */
    public function saveUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $validate = new AdministratorControlValidate();

            if (!$validate->saveUserAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $username = $request->getParam("username");
            $password = $request->getParam("password");
            $fullname = $request->getParam("fullname");
            $administrator = $request->getParam("admin");
            $avatar = $request->getParam("avatar");

            //Create object User
            $user = new User(null, $username, md5($password), $fullname, $administrator);

            $this->_dm->persist($user);
            $this->_dm->flush();

            $pathStorageImage = $this->_ci->get('settings')->get('storage.photo');

            preg_match('/data:([^;]*);base64,(.*)/', $avatar, $arrayAvatar);

            $filenameAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . $user->getId();
            file_put_contents($filenameAvatar, base64_decode($arrayAvatar[2]));

            Mail::send(
                [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ],
                [ [ 'mail' => $user->getUsername(), 'fullname' => $user->getFullname() ] ],
                "Bem Vindo.",
                "Bem vindo ao Cosmo " . $user->getFullname() . ".<br>Sua senha de acesso:" . $request->getParam('password'));

            $router = $this->_ci->get('router');
            return $response->withJson([ 'message' => 'Usuário cadastrado com sucesso.', 'callback' => $router->pathFor('administrator.control.users') ], 200);
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/user/update", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="administrator.control.users.update")
     */
    public function updateUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $validate = new AdministratorControlValidate();

            if (!$validate->updateUserAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $id = $request->getParam("id");
            $fullname = $request->getParam("fullname");
            $administrator = $request->getParam("admin");
            $avatar = $request->getParam("avatar");

            $user = $this->_dm->getRepository(User::class)->find($id);
            $user->setFullname($fullname);
            $user->setAdministrator($administrator);

            $this->_dm->persist($user);
            $this->_dm->flush();

            $pathStorageImage = $this->_ci->get('settings')->get('storage.photo');

            preg_match('/data:([^;]*);base64,(.*)/', $avatar, $arrayAvatar);

            $filenameAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . $user->getId();
            file_put_contents($filenameAvatar, base64_decode($arrayAvatar[2]));

            $router = $this->_ci->get('router');
            return $response->withJson([ 'message' => 'Usuário atualizado com sucesso.', 'callback' => $router->pathFor('administrator.control.users') ], 200);
        } else {
            return $response->withJson([ 'Requisição mal formatada!' ], 500);
        }
    }

}