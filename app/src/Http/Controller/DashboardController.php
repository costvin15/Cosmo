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
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;

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
     */
    public function indexAction(ServerRequestInterface $request, ResponseInterface $response) {
        $attributes = SessionFacilitator::getAttributeSession();

        $questionNumber = 4;

        $user = $this->_dm->getRepository(User::class)->find($attributes['id']);
        $queryBuilderHistory = $this->_dm->createQueryBuilder(HistoryActivities::class)
            ->field('user')->references($user)
            ->getQuery()->execute();

        $idActivitiesUser = [];
        foreach($queryBuilderHistory as $activies) {
            $idActivitiesUser[] = $activies->getActivity()->getId();
        }

//        $groupActivities = $this->_dm->getRepository(GroupActivities::class)->find("581a10bd7b3ceb04bb7b47cd");

        $allActivities = [];
        for($i = 0; $i <= $questionNumber - 1; $i++) {
            $qb = $this->_dm->createQueryBuilder(Activities::class)
                ->field('id')
                ->notIn($idActivitiesUser);
//                ->in(['580ff69b5bd49c0ac5809682'])
//                ->field('group');
//                ->references($groupActivities);

            $count =  $qb->getQuery()->count();

            $random_position = rand(0, $count - 1);

            $activeRandom = $qb
                ->skip($random_position)
                ->limit(1)
                ->sort('order', 'asc')
                ->getQuery()
                ->execute();

            if ($activeRandom->count() == 0) {
                break 1;
            }

            $activies = $activeRandom->getNext();
            $allActivities[] = $activies;
            $idActivitiesUser[] = $activies->getId();
        }

        $this->setAttributeView('allActivities', $allActivities);


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
     */
    public function historyAction(Request $request, Response $response) {
        $attributes = SessionFacilitator::getAttributeSession();

        $user = $this->_dm->getRepository(User::class)->find($attributes['id']);
        $queryBuilderHistory = $this->_dm->createQueryBuilder(HistoryActivities::class)
            ->field('user')->references($user)
            ->getQuery()->execute();

        $idActivitiesUser = [];
        foreach($queryBuilderHistory as $activies) {
            $idActivitiesUser[] = $activies->getActivity()->getId();
        }

        $queryBuilderAllQuestion = $this->_dm->createQueryBuilder(Activities::class)
            ->field('id')->in($idActivitiesUser)
            ->sort('order', 'asc')
            ->getQuery()->execute();

        $historyActivities = [];
        foreach($queryBuilderAllQuestion as $activies) {
            $group = $activies->getGroup();
            $historyActivities[$group->getName()][] = $activies;
        }

        return $this->view->render($response, 'View/dashboard/history/index.twig', ['attributes' => $attributes, "allGroupActivities" => $historyActivities]);
    }

    /**
     * @Get(name="/profile", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="dashboard.profile")
     */
    public function profileAction(Request $request, Response $response){
        $attributes = SessionFacilitator::getAttributeSession();
        return $this->view->render($response, "View/dashboard/profile/index.twig", ["attributes" => $attributes]);
    }
}