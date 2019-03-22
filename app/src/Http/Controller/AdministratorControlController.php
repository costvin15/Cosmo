<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 16/09/17
 * Time: 07:40
 */

namespace App\Http\Controller;

use App\Http\Controller\Validate\AdministratorUserControlValidate;
use App\Mapper\User;
use App\Model\Util\ImageBase64;
use App\Model\Util\Mail;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Mapper\Activities;
use PHPMailer\PHPMailer\Exception;
use App\Mapper\GroupActivities;
use App\Facilitator\App\SessionFacilitator;
use App\Mapper\Classes;
use App\Mapper\Challenge;
use App\Model\Category\InterfaceCategory;

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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou a página administrativa inicial.")
     */
    public function indexAction(Request $request, Response $response){
        $attributes = array(
            "data" => [
                "users" => count($this->_dm->getRepository(User::class)->findAll()),
                "activities" => count($this->_dm->getRepository(Activities::class)->findAll()),
                "group_activities" => count($this->_dm->getRepository(GroupActivities::class)->findAll()),
            ],
        );
        return $this->view->render($response, 'View/administratorcontrol/index.twig', array_merge($attributes, $this->getAttributeView()));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/user", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou a página administrativa de usuários.")
     */
    public function usersAction(Request $request, Response $response) {
        $users = $this->_dm->getRepository(User::class)->findAll();
        $this->setAttributeView("users", $users);
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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de criação de usuário.")
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
     * @Get(name="/user/modify/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.modify")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou a página administrativa de modificação de usuário.")
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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador criou um novo usuário.")
     */
    public function saveUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $validate = new AdministratorUserControlValidate();

            if (!$validate->saveUserAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $avatar = $request->getParam("avatar");
            $username = $request->getParam("username");
            $nickname = $request->getParam("nickname");
            $password = $request->getParam("password");
            $fullname = $request->getParam("fullname");
            $administrator = $request->getParam("admin");
            $fulltitle = $request->getParam("fulltitle");
            $users_with_email_entered = $this->_dm->getRepository(User::class)->findBy([
                "username" => $username
            ]);
            if (count($users_with_email_entered) > 0)
                return $response->withJson(["Este email já está sendo usado"], 500);
            unset($users_with_email_entered);

            $users_with_nickname_entered = $this->_dm->getRepository(User::class)->findBy([
                "nickname" => $nickname
            ]);
            if (count($users_with_nickname_entered) > 0)
                return $response->withJson(["Este Nome de Usuário já está sendo usado"], 500);
            unset($users_with_nickname_entered);

            $user = new User(null, $username, md5($password), $fullname, $administrator);
            $user->setNickname($nickname);

            $this->_dm->persist($user);
            $this->_dm->flush();

            if (trim($avatar) != ""){
                $pathStorageImage = $this->_ci->get('settings')->get('storage.photo');
                preg_match('/data:([^;]*);base64,(.*)/', $avatar, $arrayAvatar);
                $filenameAvatar = $pathStorageImage . DIRECTORY_SEPARATOR . $user->getId();
                file_put_contents($filenameAvatar, base64_decode($arrayAvatar[2]));
            }

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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador atualizou um usuário existente.")
     */
    public function updateUserAction(Request $request, Response $response) {
        if ($request->isXhr()) {
            $validate = new AdministratorUserControlValidate();

            if (!$validate->updateUserAction($request))
                return $response->withJson([ $validate->getError() ], 500);

            $id = $request->getParam("id");
            $avatar = $request->getParam("avatar");
            $username = $request->getParam("username");
            $nickname = $request->getParam("nickname");
            $fullname = $request->getParam("fullname");
            $administrator = $request->getParam("admin");
            $fulltitle = $request->getParam("fulltitle");
            $user = $this->_dm->getRepository(User::class)->find($id);

            $users_with_email_entered = $this->_dm->getRepository(User::class)->findBy([
                "username" => $username
            ]);
            if ($username != $user->getUsername() && count($users_with_email_entered) > 0)
                return $response->withJson(["Este email já está sendo usado"], 500);
            unset($users_with_email_entered);

            $users_with_nickname_entered = $this->_dm->getRepository(User::class)->findBy([
                "nickname" => $nickname
            ]);
            if ($nickname != $user->getNickname() && count($users_with_nickname_entered) > 0)
                return $response->withJson(["Este Nome de Usuário já está sendo usado"], 500);
            unset($users_with_nickname_entered);
            
            $block = array(
                "status" => $request->getParam("block_status") == "1" ? true : false,
                "reason" => $request->getParam("block_reason")
            );

            if ($user){
                $user->setFullname($fullname);
                $user->setUsername($username);
                $user->setNickname($nickname);
                $user->setAdministrator($administrator);
                $user->setBlocked($block);
                $user->setFullTitle($fulltitle);
            }

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
     * @Post(name="/user/remove/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.users.remove")
     */
    public function removeUserAction(Request $request, Response $response, array $args){
        $router = $this->_ci->get("router");
        $id = $args["id"];
        if (!$id)
            return $response->withJson(["Usuário não encontrado"], 500);
        
        $user = $this->_dm->getRepository(User::class)->find($id);
        $this->_dm->remove($user);
        $this->_dm->flush();

        return $response->withJson(["message" => "Usuário removido com sucesso", "callback" => $router->pathFor("administrator.control.users")], 200);
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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou a página administrativa de atividades.")
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
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de criação de atividade.")
     */
    public function newActivityAction(Request $request, Response $response){
        $this->setAttributeView("formCreate", true);
        $this->setAttributeView("categories", InterfaceCategory::CATEGORIES);
        $this->setAttributeView("group_activities", $this->_dm->getRepository(GroupActivities::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/activities/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/activity/modify/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities.modify")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de modificação de atividade.")
     */
    public function modifyActivityAction(Request $request, Response $response, array $args){
        $id = $args["id"];

        if (!$id)
            throw new \Exception("Atividade não encontrada");
        
        $activity = $this->_dm->getRepository(Activities::class)->find($id);
        if (!$activity)
            throw new \Exception("Atividade não encontrada");
        
        $this->setAttributeView("formUpdate", true);
        $this->setAttributeView("categories", InterfaceCategory::CATEGORIES);
        $this->setAttributeView("activity", $activity->toArray());
        $this->setAttributeView("group_activities", $this->_dm->getRepository(GroupActivities::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/activities/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/activity/remove/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities.remove")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador removeu uma atividade.")
     */
    public function removeActivityAction(Request $request, Response $response, array $args){
        $router = $this->_ci->get("router");
        $id = $args["id"];
        if (!$id)
            return $response->withRedirect($router->pathFor("administrator.control.activities"));
        
        $activity = $this->_dm->getRepository(Activities::class)->find($id);
        $this->_dm->remove($activity);
        $this->_dm->flush();

        return $response->withRedirect($router->pathFor("administrator.control.activities"));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/activity/save", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.activities.save")
     * @Log(type="INFO", persist={"verb", "session"}, message="O adminstrador salvou uma atividade.")
     */
    public function saveActivityAction(Request $request, Response $response){
        if ($request->isXhr()){
            try {
                $attributes = SessionFacilitator::getAttributeSession();

                $id = $request->getParam("id");
                $title = $request->getParam("title");
                $question = $request->getParam("question");
                $fullquestion = $request->getParam("fullquestion");
                $group = $this->_dm->getRepository(GroupActivities::class)->findBy(["name" => $request->getParam("group")])[0];
                $input_description = $request->getParam("input_description");
                $input_example = $request->getParam("input_example");
                $output_description = $request->getParam("output_description");
                $output_example = $request->getParam("output_example");
                $casos_de_teste = $request->getParam("casos_testes");

                $dateCreate = date("Y-m-d H:i:s");
                $uploader = $this->_dm->getRepository(User::class)->find($attributes['id']);
                $moedas = $request->getParam("moedas");
                $xp = $request->getParam("xp");
                $cust = $request->getParam("cust");
                $category = $request->getParam("category");
                if ($id)
                    $activity = $this->_dm->getRepository(Activities::class)->find($id);
                else
                    $activity = new Activities();

                if (!$activity)
                    return $response->withJson(["Atividade não encontrada."], 500);
                
                $activity->setTitle($title);
                $activity->setQuestion($question);
                $activity->setFullquestion($fullquestion);
                $activity->setGroup($group);
                $activity->setInputDescription($input_description);
                $activity->setOutputDescription($output_description);
                $activity->setActivityExample(array(array(
                    "in" => new \MongoBinData($input_example, \MongoBinData::GENERIC),
                    "out" => new \MongoBinData($output_example, \MongoBinData::GENERIC),
                )));
                $activity->setActivities(array(
                    "tasks" => $casos_de_teste,
                    "plugin" => "App\\Plugin\\Activities\\Problems\\Mapper\\Config",
                ));
                $activity->setGroup($group);
                $activity->setMoedas($moedas);
                $activity->setXP($xp);
                $activity->setCust($cust);
                $activity->setCategory($category);

                if (!$id){
                    $activity->setDateCreate($dateCreate);
                    $activity->setUploader($uploader);
                }

                $this->_dm->persist($activity);
                $this->_dm->flush();

                $router = $this->_ci->get("router");
                
                if ($id)
                    return $response->withJson(["message" => "Atividade atualizada com sucesso!", "callback" => $router->pathFor("administrator.control.activities")], 200);    
                else
                    return $response->withJson(["message" => "Atividade cadastrada com sucesso!", "callback" => $router->pathFor("administrator.control.activities")], 200);
            } catch (\Exception $e){
                return $response->withJson([$e->getMessage()], 500);
            }
        } else
            return $response->withJson(["Requisição mal formatada."], 500);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/skills", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.groupactivities")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de grupos de atividades.")
     */
    public function groupActivitiesAction(Request $request, Response $response){
        $this->setAttributeView("group_activities", $this->_dm->getRepository(GroupActivities::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/groupactivities/index.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/skills/create", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.groupactivities.create")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de criação de grupo de atividade.")
     */
    public function newGroupActivityAction(Request $request, Response $response){
        $this->setAttributeView("formCreate", true);
        return $this->view->render($response, "View/administratorcontrol/groupactivities/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/skills/modify/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.groupactivities.modify")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o página adminstrativa de modificação de grupo de atividade.")
     */
    public function modifyGroupActivityAction(Request $request, Response $response, array $args){
        $id = $args["id"];

        if (!$id)
            throw new \Exception("Grupo de Atividades não encontrado");
        
        $group = $this->_dm->getRepository(GroupActivities::class)->find($id);
        if (!$group)
            throw new \Exception("Grupo de Atividades não encontrado");
        
        $this->setAttributeView("formUpdate", true);
        $this->setAttributeView("group_activity", $group->toArray());
        return $this->view->render($response, "View/administratorcontrol/groupactivities/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/skills/save", alias="administrator.control.groupactivities.save")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador salvou um grupo de atividade.")
     */
    public function saveGroupActivityAction(Request $request, Response $response){
        if ($request->isXhr()){
            $id = $request->getParam("id");
            $name = $request->getParam("name");
            $visible = $request->getParam("visible");
            $tags = array();
            $tags[0] = $request->getParam("tags");

            if ($id)
                $group = $this->_dm->getRepository(GroupActivities::class)->find($id);
            else
                $group = new GroupActivities();

            if (!$group)
                return $response->withJson(["Grupo não encontrado."], 500);
            
            $group->setName($name);
            $group->setVisible($visible);
            $group->setTags($tags);

            $this->_dm->persist($group);
            $this->_dm->flush();

            $router = $this->_ci->get("router");
            if ($id)
                return $response->withJson(["message" => "Grupo de Atividade atualizado com sucesso!", "callback" => $router->pathFor("administrator.control.groupactivities")], 200);
            else
                return $response->withJson(["message" => "Grupo de Atividade cadastrado com sucesso!", "callback" => $router->pathFor("administrator.control.groupactivities")], 200);
        } else
            return $response->withJson(["Requisição mal formatada."], 500);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/skills/remove/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.groupactivities.remove")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador removeu um grupo de atividade.")
     */
    public function removeGroupActivityAction(Request $request, Response $response, array $args){
        $router = $this->_ci->get("router");
        $id = $args["id"];
        if (!$id)
            return $response->withRedirect($router->pathFor("administrator.control.groupactivities"));
        
        $group = $this->_dm->getRepository(GroupActivities::class)->find($id);
        $this->_dm->remove($group);
        $this->_dm->flush();

        return $response->withRedirect($router->pathFor("administrator.control.index"));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @Get(name="/skills/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.groupactivities.view")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador removeu um grupo de atividade.")
     */
    public function viewGroupActivityAction(Request $request, Response $response, array $args){
        $group = $this->_dm->getRepository(GroupActivities::class)->find($args["id"]);
        if (!$group)
            return $response->withJson(["Grupo não encontrado."], 500);
        $this->setAttributeView("group", $group->toArray());
        return $this->view->render($response, "View/administratorcontrol/groupactivities/view.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/skills/challenges/new/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.skills.challenges.new")
     */
    public function newChallengeSkillAction(Request $request, Response $response, array $args){
        $this->setAttributeView("formCreate", true);
        $this->setAttributeView("skill_id", $args["id"]);
        $this->setAttributeView("questions", $this->_dm->getRepository(Activities::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/groupactivities/challenges/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/classes/", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador acessou o painel de turmas.")
     */
    public function classesAction(Request $request, Response $response){
        $this->setAttributeView("classes", $this->_dm->getRepository(Classes::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/classes/index.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/classes/new", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.new")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador acessou o painel de criação de turmas.")
     */
    public function newClassesAction(Request $request, Response $response){
        $this->setAttributeView("formCreate", true);
        return $this->view->render($response, "View/administratorcontrol/classes/form.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/classes/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.view")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="O administrador acessou uma turma.")
     */
    public function viewClassesAction(Request $request, Response $response, array $args){
        $id = $args["id"];
        $class = $this->_dm->getRepository(Classes::class)->find($id);
        if (!$class)
            return $response->withJson(["Turma não encontrada!"], 500);
        // if ($class->getAdministrator()->getId() !== SessionFacilitator::getAttributeSession()["id"])
        //     return $response->withJson(["Desculpe-nos, mas você não possui os privilégios necessários para isto."], 500);
        $this->setAttributeView("class", $class->toArray());
        $this->setAttributeView("users", $class->getStudents());
        $this->setAttributeView("groups", $this->_dm->getRepository(GroupActivities::class)->findAll());
        // $db_challenges = $class->getChallenges();
        // $challenges = array();
        // for ($i = 0; $i < count($db_challenges); $i++){
        //     $challenges[$i] = $db_challenges[$i]->toArray();
        //     for ($j = 0; $j < count($challenges[$i]["questions"]); $j++){
        //         $challenges[$i]["questions"][$j]["id"] = $this->_dm->getRepository(Activities::class)->find($challenges[$i]["questions"][$j]["id"]);
        //     }
        // }
        // $this->setAttributeView("challenges", $challenges);
        return $this->view->render($response, "View/administratorcontrol/classes/view.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/classes/save", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.save")
     */
    public function saveClassAction(Request $request, Response $response){
        if ($request->isXhr()){
            $id = $request->getParam("id");
            $title = $request->getParam("title");
            $code = $request->getParam("code");

            if ($id)
                $class = $this->_dm->getRepository(Classes::class)->find($id);
            else
                $class = new Classes();
            
            $attributes = SessionFacilitator::getAttributeSession();
            if (!$attributes)
                return $response->withJson(["Você precisa estar logado para isto."], 500);

            $class->setTitle($title);
            $class->setCode($code);
            $class->setAdministrator($this->_dm->getRepository(User::class)->find($attributes["id"]));

            $this->_dm->persist($class);
            $this->_dm->flush();

            $router = $this->_ci->get("router");
            return $response->withJson(["message" => "Turma cadastrada com sucesso!", "callback" => $router->pathFor("administrator.control.classes")]);
        } else
            return $response->withJson(["Requisição mal formatada"], 500);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/classes/insert_students", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.insert_students")
     */
    public function insertStudentsClassAction(Request $request, Response $response){
        $id = $request->getParam("id");
        $students = $request->getParam("students");
        $count = 0;
        $class = $this->_dm->getRepository(Classes::class)->find($id);

        if (!$class)
            return $response->withJson(["Turma não encontrada"], 500);
        if ($students)
            for ($i = 0; $i < count($students); $i++){
                $current = $this->_dm->getRepository(User::class)->findBy(array("username" => $students[$i]));
                if ($current[0])
                    if ($current[0]->getClass() !== $class){
                        $current[0]->setClass($class);
                        $this->_dm->persist($current[0]);
                        $count++;
                    }
            }

        $this->_dm->flush();
        $router = $this->_ci->get("router");
        return $response->withJson(["message" => "Novos {$count} alunos foram adicionados à turma atual.", "callback" => $router->pathFor("administrator.control.classes.view", array("id" => $id))], 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/class/insert_skills", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.insert_skills")
     */
    public function insertSkillClassAction(Request $request, Response $response){
        $id = $request->getParam("id");
        $skills = $request->getParam("skills");
        $class = $this->_dm->getRepository(Classes::class)->find($id);
        $count = 0;

        if (!$class)
            return $response->withJson(["Turma não encontrada"], 500);
        for ($i = 0; $i < count($skills); $i++){
            $current = $this->_dm->getRepository(GroupActivities::class)->find($skills[$i]);
            if ($current)
                if ($current->getClass() !== $class){
                    $current->setClass($class);
                    $this->_dm->persist($current);
                    $count++;
                }
        }

        $this->_dm->flush();
        $router = $this->_ci->get("router");
        return $response->withJson(["message" => "Foram adicionadas {$count} novas habilidades à turma atual.", "callback" => $router->pathFor("administrator.control.classes.view", array("id" => $id))], 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/class/challenges/save", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.challenges.save")
     */
    public function saveChallengeClassAction(Request $request, Response $response){
        $class = $this->_dm->getRepository(Classes::class)->find($request->getParam("class"));
        $router = $this->_ci->get("router");
        if (!$class)
            return $response->withRedirect($router->pathFor("administrator.control.classes"));
        $title = $request->getParam("title");
        $opening = date("Y-m-d H:i:s", strtotime($request->getParam("opening")));
        $validity = date("Y-m-d H:i:s", strtotime($request->getParam("validity")));
        $type = $request->getParam("type");
        $questions = $request->getParam("questions");

        if (!$class)
            return $response->withJson(["message" => "Você precisa estar em uma turma!"], 500);

        $challenge = new Challenge();
        $challenge->setClass($class);
        $challenge->setTitle($title);
        $challenge->setOpening($opening);
        $challenge->setValidity($validity);
        $challenge->setType($type);
        $challenge->setQuestions($questions);

        $this->_dm->persist($challenge);
        $this->_dm->flush();

        return $response->withJson(["message" => "Desafio adicionado com sucesso!", "callback" => $router->pathFor("administrator.control.classes.view", ["id" => $request->getParam("class")])], 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/class/challenges/remove/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.challenges.remove")
     */
    public function removeChallengeClassAction(Request $request, Response $response, array $args){
        $id = $args["id"];
        if (!$id)
            return $response->withRedirect($router->pathFor("administrator.control.classes"));
        $challenge = $this->_dm->getRepository(Challenge::class)->find($id);
        $class = $challenge->getClass();
        if (!$challenge)
            return $response->withRedirect($router->pathFor("administrator.control.classes.view", ["id" => $class->getId()]));
        $this->_dm->remove($challenge);
        $this->_dm->flush();
        $router = $this->_ci->get("router");
        return $response->withRedirect($router->pathFor("administrator.control.classes.view", ["id" => $class->getId()]));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/class/challenges/edit/{id}", middleware={"App\Http\Middleware\AdministratorSessionMiddleware"}, alias="administrator.control.classes.challenges.edit")
     */
    public function editChallengeClassAction(Request $request, Response $response, array $args){
        $id = $args["id"];
        if (!$id)
            return $response->withRedirect($router->pathFor("administrator.control.classes"));
        $db_challenge = $this->_dm->getRepository(Challenge::class)->find($id);
        $class = $db_challenge->getClass();
        if (!$db_challenge)
            return $response->withRedirect($router->pathFor("administrator.control.classes.view", ["id" => $class->getId()]));
        $challenge = $db_challenge->toArray();
        for ($j = 0; $j < count($challenge["questions"]); $j++){
            $challenge["questions"][$j]["id"] = $this->_dm->getRepository(Activities::class)->find($challenge["questions"][$j]["id"]);
        }
        $this->setAttributeView("challenge", $challenge);
        $this->setAttributeView("formUpdate", true);
        $this->setAttributeView("class_id", $args["id"]);
        $this->setAttributeView("questions", $this->_dm->getRepository(Activities::class)->findAll());
        return $this->view->render($response, "View/administratorcontrol/classes/challenges/form.twig", $this->getAttributeView());
    }
}