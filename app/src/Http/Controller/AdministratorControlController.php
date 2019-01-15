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
use App\Mapper\Activities;
use PHPMailer\PHPMailer\Exception;

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
     * @Get(name="/", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.index")
     */
    public function indexAction(Request $request, Response $response){
        $attributes = array(
            "data" => [
                "users" => count($this->_dm->getRepository(User::class)->findAll()),
                "activities" => count($this->_dm->getRepository(Activities::class)->findAll()),
            ],
        );
        return $this->view->render($response, 'View/administratorcontrol/index.twig', array_merge($attributes, $this->getAttributeView()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users")
     */
    public function usersAction(Request $request, Response $response) {
        return $this->view->render($response, 'View/administratorcontrol/users/index.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user/all", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.all")
     */
    public function findAllUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $router = $this->_ci->get('router');
            $userCollection = $this->_dm->getRepository(User::class)->findAll();

            $arrayUser = [];
            foreach ($userCollection as $user) {
                $arrayUserTemp = $user->toArray();
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
     * @Get(name="/user/create", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.new")
     */
    public function newUserAction(Request $request, Response $response) {
        $this->setAttributeView('show_adm', true);
        $this->setAttributeView('formCreate', true);
        return $this->view->render($response, 'View/administratorcontrol/users/form.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @throws \Exception
     * @return mixed
     * @Get(name="/user/modify/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.new")
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

        $fileAvatar = "";
        if (!file_exists($pathStorageImage . DIRECTORY_SEPARATOR . $user->getId())) {
            $fileAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . 'default.png';
        } else {
            $fileAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . $user->getId();
        }

        $avatarBase64 = $image64->castPathFile($fileAvatar);

        $this->setAttributeView('id', $id);
        $this->setAttributeView('user', $user->toArray());
        return $this->view->render($response, 'View/administratorcontrol/users/form.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/user/save", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.save")
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
     * @Post(name="/user/update", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.update")
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

            $block = array(
                "status" => $request->getParam("block_status") == "1" ? true : false,
                "reason" => $request->getParam("block_reason")
            );

            $user = $this->_dm->getRepository(User::class)->find($id);
            $user->setFullname($fullname);
            $user->setAdministrator($administrator);
            $user->setBlocked($block);

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

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/user/image/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.controls.users.image")
     */
    public function userImageAction(Request $request, Response $response, array $args){
        if (!$args["id"])
            return $response->withJson(["message" => "O ID é obrigatório"], 500);
        
        $id = $args["id"];
        $image64 = new ImageBase64();
        $path_storage = $this->_ci->get("settings")->get("storage.photo");
        $filename = "";

        if (file_exists($path_storage . DIRECTORY_SEPARATOR . $id))
            $filename = $path_storage . DIRECTORY_SEPARATOR . $id;
        else
            $filename = $path_storage . DIRECTORY_SEPARATOR . "default.png";

        return $image64->castPathFile($filename);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/activity", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities")
     */
    public function activitiesAction(Request $request, Response $response){
        $activities = $this->_dm->getRepository(Activities::class)->findAll();
        $this->setAttributeView("activities", $activities);

        return $this->view->render($response, "View/administratorcontrol/activities/index.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/activity/create", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities.new")
     */
    public function newActivityAction(Request $request, Response $response){
        $this->setAttributeView("formCreate", true);
        return $this->view->render($response, "View/administratorcontrol/activities/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/activity/modify/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities.modify")
     */
    public function modifyActivityAction(Request $request, Response $response, array $args){
        $id = $args["id"];

        if (!$id)
            throw new \Exception("Atividade não encontrada");
        
        $activity = $this->_dm->getRepository(Activities::class)->find($id);
        if (!$activity)
            throw new \Exception("Atividade não encontrada");
        
        $this->setAttributeView("formUpdate", true);
        $this->setAttributeView("activity", $activity->toArray());
        return $this->view->render($response, "View/administratorcontrol/activities/form.twig", $this->getAttributeView());
    }
}