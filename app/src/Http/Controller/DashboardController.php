<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 18/10/16
 * Time: 13:26
 */

namespace App\Http\Controller;
use App\Facilitator\App\SessionFacilitator;
use App\Mapper\Activities;
use App\Mapper\GroupActivities;
use App\Mapper\HistoryActivities;
use App\Mapper\User;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\Util\ImageBase64;
use App\Mapper\Classes;
use App\Mapper\ChallengeHistory;
use App\Mapper\CategoryActivities;
use Twig\Extension\StagingExtension;
use App\Mapper\Star;
use App\Model\Category\InterfaceCategory;
use App\Auth\Adapters\UserWithPassword;
use SlimAuth\SlimAuthFacade;

/**
 * Class DashboardController
 * @package App\Http\Controller
 * @Controller
 * @Route('/dashboard')
 */
class DashboardController extends AbstractController
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @Get(name="/", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.index")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou a página inicial.")
     */
    public function indexAction($request, $response){
        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);

        if ($user && $user->getClass()){
            $user_history = $this->_dm->createQueryBuilder(HistoryActivities::class)
                ->field("user")->references($user)->getQuery()->execute();
            $user_history_ids = array();
            foreach ($user_history as $activity)
                $user_history_ids[] = $activity->getActivity()->getId();

            $groups = $this->_dm->getRepository(GroupActivities::class)->findBy(array("class" => $user->getClass()));
            for ($i = 0; $i < count($groups); $i++){
                $group = $groups[$i]->toArray();
                $group["activities"] = $this->_dm->createQueryBuilder(Activities::class)
                    ->field("group")->references($groups[$i])
                    ->field("id")->notIn($user_history_ids)->getQuery()->execute();
                $groups[$i] = $group;
            }
            $this->setAttributeView("groups", $groups);

            $stars =  $this->_dm->getRepository(Star::class)->findStarWithUser($user);
            $this->setAttributeView("stars", $stars);

            // $db_challenges = $user->getClass()->getChallenges();
            // $challenges = array();
            // for ($i = 0, $k = 0; $i < count($db_challenges); $i++){
            //     $search = $this->_dm->getRepository(ChallengeHistory::class)->findBy(array(
            //         "user" => $user,
            //         "challenge" => $db_challenges[$i]
            //     ));
            //     if (count($search) == 0){
            //         $challenges[$k] = $db_challenges[$i]->toArray();
            //         for ($j = 0; $j < count($challenges[$k]["questions"]); $j++){
            //             $challenges[$k]["questions"][$j]["id"] = $this->_dm->getRepository(Activities::class)->find($challenges[$k]["questions"][$j]["id"]);
            //         }
            //         $k++;
            //     }
            // }
            // $this->setAttributeView("challenges", $challenges);
            $this->setAttributeView("class", $user->getClass());
        }

        // $questionNumber = 4;

        // $user = $this->_dm->getRepository(User::class)->find($attributes['id']);
        // $queryBuilderHistory = $this->_dm->createQueryBuilder(HistoryActivities::class)
            // ->field('user')->references($user)
            // ->getQuery()->execute();

        // $idActivitiesUser = [];
        // foreach($queryBuilderHistory as $activies) {
            // $idActivitiesUser[] = $activies->getActivity()->getId();
        // }

//        $groupActivities = $this->_dm->getRepository(GroupActivities::class)->find("581a10bd7b3ceb04bb7b47cd");

        // $allActivities = [];
        // for($i = 0; $i <= $questionNumber - 1; $i++) {
            // $qb = $this->_dm->createQueryBuilder(Activities::class)
                // ->field('id')
                // ->notIn($idActivitiesUser);
//                ->in(['580ff69b5bd49c0ac5809682'])
//                ->field('group');
//                ->references($groupActivities);

            // $count =  $qb->getQuery()->count();

            // $random_position = rand(0, $count - 1);

            // $activeRandom = $qb
                // ->skip($random_position)
                // ->limit(1)
                // ->sort('order', 'asc')
                // ->getQuery()
                // ->execute();

            // if ($activeRandom->count() == 0) {
                // break 1;
            // }

            // $activies = $activeRandom->getNext();
            // $allActivities[] = $activies;
            // $idActivitiesUser[] = $activies->getId();
        // }

        // $this->setAttributeView('allActivities', $allActivities);
        return $this->view->render($response, 'View/dashboard/index/index.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @Post(name="/question/one", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.question.one")
     */
    public function getOneQuestion(Request $request, Response $response) {
        $questionsAttr = json_decode($request->getParam('questions', null));

        $attributes = SessionFacilitator::getAttributeSession();

        $user = $this->_dm->getRepository(User::class)->find($attributes['id']);
        $queryBuilderHistory = $this->_dm->createQueryBuilder(HistoryActivities::class)
            ->field('user')->references($user)
            ->getQuery()->execute();

        $idActivitiesUser = [];
        foreach($queryBuilderHistory as $activies) {
            $idActivitiesUser[] = $activies->getActivity()->getId();
        }

        $idActivitiesUser = array_merge($idActivitiesUser, $questionsAttr);

        $groupActivities = $this->_dm->getRepository(GroupActivities::class)->find("581a10bd7b3ceb04bb7b47cd");

        $qb = $this->_dm->createQueryBuilder(Activities::class)
            ->field('id')
            ->notIn($idActivitiesUser)
            ->field('group')->references($groupActivities);

        $count =  $qb->getQuery()->count();

        $random_position = rand(0, $count - 1);

        $activeRandom = $qb
            ->skip($random_position)
            ->limit(1)
            ->sort('order', 'asc')
            ->getQuery()
            ->execute();

        $activies = $activeRandom->getNext();

        if ($activies == null) {
            return $response->withJson([ 'Não é possível pular a questão'], 500);
        } else {
            $group = $activies->getGroup();
            $tags = $group->getTags();
            $arrayActivities = [
                'title' => $activies->getTitle(),
                'description' => 'Publicado por: Carlos Salles - ' . $activies->getDateCreate()->format('d/m/Y'),
                'tags' => $tags,
                'question' => $activies->getQuestion(),
                'id' => $activies->getId(),
                'origin' => $request->getParam('origin'),
            ];

            return $response->withJson($arrayActivities);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     * @Get(name="/history", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.history")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o histórico.")
     */
    public function historyAction(Request $request, Response $response) {
        $attributes = SessionFacilitator::getAttributeSession();
        $current_user = $this->_dm->getRepository(User::class)->find($attributes["id"]);
        $history = $this->_dm->getRepository(HistoryActivities::class)->findBy([ "user" => $current_user ]);

        for ($i = 0; $i < count($history); $i++)
            $history[$i] = $history[$i]->toArray();

        $this->setAttributeView("history", $history);

        return $this->view->render($response, 'View/dashboard/history/index.twig', $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     *
     * @Get(name="/profile/edit", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile.edit")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o seu perfil.")
     */
    public function profileAction(Request $request, Response $response){
        $attributes = SessionFacilitator::getAttributeSession();
        return $this->view->render($response, "View/dashboard/profile/profile_edit.twig", ["attributes" => $attributes]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/profile/update", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile.update")
     * @Log(type="NOTICE", persist={"verb", "attributes", "session"}, message="Atualizou informações do seu perfil.")
     */
    public function profileUpdateAction(Request $request, Response $response){
        if ($request->isXhr()){
            $avatar = $request->getParam("avatar");
            $fullname = $request->getParam("fullname");
            $nickname = $request->getParam("nickname");
            $sexo = $request->getParam("sexo");
            $code = $request->getParam("code");
            // $fulltitle = $request->getParam("fulltitle");


            $attributes = SessionFacilitator::getAttributeSession();

            $user = $this->_dm->getRepository(User::class)->find($attributes['id']);
            if($user->getXP() > 25){
                $user->setFullTitle("Escudeiro(a)");
            }elseif($user->getXP() > 50){
                $user->setFullTitle("Visconde/Vinscodessa");
            }elseif($user->getXP() > 75){
                $user->setFullTitle("Duque/Duquesa");
            }elseif($user->getXP() > 100){
                $user->setFullTitle("Imperador/Imperatriz");
            }
            $user->setSexo($sexo);
            $user->setFullname($fullname);
            $user->setNickname($nickname);

            if (trim($code) != ""){
                $classes = $this->_dm->getRepository(Classes::class)->findBy(array("code" => $code));
                if (count($classes) == 0)
                    return $response->withJson(["message" => "O código inserido não corresponde a nenhuma turma."], 500);
                $user->setClass($classes[0]);
            }

            // $user->setFullTitle($fulltitle);

            $this->_dm->persist($user);
            $this->_dm->flush();

            $path_storage_image = $this->_ci->get("settings")->get("storage.photo");
            preg_match('/data:([^;]*);base64,(.*)/', $avatar, $matches);

            $filename = $path_storage_image . DIRECTORY_SEPARATOR . $user->getId();
            file_put_contents($filename, base64_decode($matches[2]));

            $router = $this->_ci->get("router");

            UserWithPassword::updateSession($nickname);

            return $response->withJson(["message" => "Seu perfil foi atualizado com sucesso."], 200);
        } else
            return $response->withJson(["message" => "Requisição mal formatada"], 500);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     *
     * @Get(name="/profile", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o seu perfil.")
     */
    public function visitYourProfile(Request $request, Response $response){
        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);
        if ($user && $user->getClass()){
            $user_history = $this->_dm->createQueryBuilder(HistoryActivities::class)
                ->field("user")->references($user)->getQuery()->execute();
            $user_history_ids = array();
            foreach ($user_history as $activity)
                $user_history_ids[] = $activity->getActivity()->getId();

            $groups = $this->_dm->getRepository(GroupActivities::class)->findBy(array("class" => $user->getClass()));
            for ($i = 0; $i < count($groups); $i++){
                $group = $groups[$i]->toArray();
                $group["activities"] = $this->_dm->createQueryBuilder(Activities::class)
                    ->field("group")->references($groups[$i])
                    ->field("id")->notIn($user_history_ids)->getQuery()->execute();
                $groups[$i] = $group;
            }
            $this->setAttributeView("groups", $groups);
            $this->setAttributeView("user", $user);
            $this->setAttributeView("class", $user->getClass());
        }
        return $this->view->render($response, "View/dashboard/profile/index.twig", $this->getAttributeView());

    }

     /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     *
     * @Get(name="/profile/{id}", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile.visit")
     */
    public function visitAnotherProfile(Request $request, Response $response, array $args){
        $this->setAttributeView("user", $this->_dm->getRepository(User::class)->find($args["id"]));
        return $this->view->render($response, "View/dashboard/profile/profile_visit.twig", $this->getAttributeView());
    }


    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/profile/close", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile.close")
     * @Log(type="NOTICE", persist={"verb", "attributes", "session"}, message="Encerrou o seu perfil.")
     */
    public function closeAccountAction(Request $request, Response $response){
        if ($request->isXhr()){
            $attributes = SessionFacilitator::getAttributeSession();

            $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);
            $this->_dm->remove($user);
            $this->_dm->flush();

            $router = $this->_ci->get("router");
            return $response->withJson(["message" => "Sua conta foi encerrada.", "callback" => $router->pathFor("login.logout")], 200);
        } else
            return $response->withJson(["Requisição mal formatada"], 500);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Get(name="/ranking", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.ranking")
     * @Log(type="INFO", persist={"verb", "attributes", "session"}, message="Acessou o ranking.")
     */
    public function rankingAction(Request $request, Response $response){
        $attributeSession = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributeSession["id"]);
        $router = $this->_ci->get("router");
        if ($user)
            $this->setAttributeView("attributes", $user->toArray());
        else
            return $response->withRedirect($router->pathFor("login.index"));
        return $this->view->render($response, "View/dashboard/ranking/index.twig", $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/ranking", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.ranking")
     */
    public function getRakingAction(Request $request, Response $response){
        $usuarios = $this->_dm->getRepository(User::class)->findAll();

        for ($i = 0; $i < count($usuarios) - 1; $i++)
            for ($j = $i + 1; $j < count($usuarios); $j++)
                if ($usuarios[$i]->getAnsweredActivities() < $usuarios[$j]->getAnsweredActivities()){
                    $aux = $usuarios[$i];
                    $usuarios[$i] = $usuarios[$j];
                    $usuarios[$j] = $aux;
                }
        for ($i = 0; $i < count($usuarios); $i++)
            $usuarios[$i] = $usuarios[$i] ->toRankingArray();

        return $response->withJson($usuarios, 200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @Get(name="/skill/{idGroup}", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.skill")
     */
    public function skillCategoryAction(Request $request, Response $response, array $args){
        $idGroup = $args["idGroup"];

        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);

        $category = $this->_dm->getRepository(CategoryActivities::class)->findCategory(InterfaceCategory::REQUIRED);
        $group = $this->_dm->getRepository(GroupActivities::class)->find($idGroup);
        $star = $this->_dm->getRepository(Star::class)->findStar($user,$group,$category);


        $this->setAttributeView("required", InterfaceCategory::REQUIRED);
        if($star)
            $this->setAttributeView("blocked", !$star->getCompleted());
        else
            $this->setAttributeView("blocked",true);
        $this->setAttributeView("group", $group);
        $this->setAttributeView("categories", $this->_dm->getRepository(CategoryActivities::class)->findAll());
        $star = $this->_dm->getRepository(Star::class)->findStar($user,$group,$category);

        if($star){

        }
        return $this->view->render($response, "View/dashboard/skill/categories.twig", $this->getAttributeView());
    }
     /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @Get(name="/skill/{idGroup}/{idCategory}", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.skill.questions")
     */
    public function skillAction(Request $request, Response $response, array $args){
        $idGroup = $args["idGroup"];
        $idCategory = $args["idCategory"];

        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);

        $category = $this->_dm->getRepository(CategoryActivities::class)->find($idCategory);
        $group = $this->_dm->getRepository(GroupActivities::class)->find($idGroup);
        $star = $this->_dm->getRepository(Star::class)->findStar($user,$group,$category);

        if(!$star){
            $star = new Star();
            $star->setUser($user);
            $star->setGroupActivities($group);
            $star->setCategoryActivities($category);
            $star->setTimeStart(time());
            $this->_dm->persist($star);
            $this->_dm->flush();
        }

        $activities = [];
        foreach($group->getActivity() as $activity){
            if($activity->getCategory() == $category->getCategory()){
                $activities[] = $activity;
            }
        }
        $this->setAttributeView("price", false);
        if($category->getCategory() == InterfaceCategory::CHALLENGE){
            $this->setAttributeView("price", true);
            $this->setAttributeView("payments", $user->getPurchasedActivities());
            $this->setAttributeView("coins", $user->getMoedas());
        }
        $this->setAttributeView("activities", $activities);
        $this->setAttributeView("idGroup", $idGroup);
        return $this->view->render($response, "View/dashboard/skill/index.twig", $this->getAttributeView());
    }
}
