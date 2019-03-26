<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 18/10/16
 * Time: 14:56
 */

namespace App\Http\Controller;
use App\Facilitator\App\SessionFacilitator;
use App\Mapper\Activities;
use App\Mapper\GroupActivities;
use App\Mapper\HistoryActivities;
use App\Mapper\User;
use Interop\Container\ContainerInterface;
use App\Validator;
use App\Mapper\PVP;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @package App\Http\Controller
 * @Controller
 * @Route("/activities")
 */
class ActivitiesController extends AbstractController
{

    /**
     * @var Activities;
     */
    private $activity;

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
     * @param array $args
     * @return mixed
     * @Get(name="/{id}", alias="activities")
     */
    public function activitiesAction(Request $request, Response $response, array $args) {
        $idActivity = $args["id"];
        $this->activity = $this->_dm->getRepository(Activities::class)->find($idActivity);
        $this->setAttributeView("activity", $this->activity);
        if ($request->getParam("challenge-type")){
            $this->setAttributeView("type", $request->getParam("challenge-type"));
            $this->setAttributeView("challenge_id", $request->getParam("challenge-id"));
            $this->setAttributeView("level", $request->getParam("challenge-level"));
        }
        return $this->view->render($response, $this->activity->getView(), $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/pvp/{challenge}", alias="activities.pvp")
     */
    public function pvpActivitiesAction(Request $request, Response $response, array $args){
        $challenge = $args["challenge"];
        $attributes = $this->getAttributeView();
        
        $user = $this->_dm->getRepository(User::class)->find($attributes["attributes"]["id"]);
        $challenge = $this->_dm->getRepository(PVP::class)->find($challenge);
        $this->activity = $challenge->getActivity();
        $router = $this->_ci->get("router");

        if ($challenge->getCompleted())
            return $response->withRedirect($router->pathFor("dashboard.index"));
        
        $this->setAttributeView("activity", $this->activity);

        if ($user->getId() === $challenge->getChallenger()->getId())
            if (!$challenge->getStartTimeChallenger())
                $challenge->setStartTimeChallenger(microtime(true));
        if ($user->getId() === $challenge->getChallenged()->getId())
            if (!$challenge->getStartTimeChallenged()){
                $challenge->setAccepted(true);
                $challenge->setStartTimeChallenged(microtime(true));
            }
        
        $this->_dm->persist($challenge);
        $this->_dm->flush();
        
        $this->setAttributeView("challenge", $challenge);

        if ($challenge->getStartTimeChallenger() && $challenge->getSubmissionTimeChallenger())
            $this->setAttributeView("challenger_time", $challenge->getSubmissionTimeChallenger() - $challenge->getStartTimeChallenger());
        if ($challenge->getStartTimeChallenged() && $challenge->getSubmissionTimeChallenged())
            $this->setAttributeView("challenged_time", $challenge->getSubmissionTimeChallenged() - $challenge->getStartTimeChallenged());

        return $this->view->render($response, $this->activity->getView(), $this->getAttributeView());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @Get(name="/{id}/decrepted", alias="activities.view")
     */
    public function viewAnswerAction(Request $request, Response $response, array $args){
        $id = $args["id"];
        if (!$id)
            throw new \Exception("Resposta não encontrada");
        $answer = $this->_dm->getRepository(HistoryActivities::class)->find($id);
        if (!$answer)
            throw new \Exception("Resposta não encontrada");
        $this->activity = $answer->getActivity();
        return $this->view->render($response, $this->activity->getView(), ["attributes" => SessionFacilitator::getAttributeSession(), "activity" => $this->activity, "answer" => $answer]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @Post(name="/submit", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="submitactivity")
     */
    public function submitActivityAction(Request $request, Response $response) {
        $params = $request->getParsedBody();
        $idActivity = $params['id_activity'];
        $challenge = $params["challenge"];
        
        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes['id']);

        $activity = $this->_dm->getRepository(Activities::class)->find($idActivity);
        $validateClass = $activity->getValidate();

        $validateInstanced = new $validateClass();
        $returnValidate = $validateInstanced($params);
        
        $params["dateini"] = $returnValidate->timeIn;
        $params["datefim"] = $returnValidate->timeOut;

        $validateInstanced->saveAttempt($params, $returnValidate);

        if ($returnValidate->answer) {
            if ($challenge){
                $instance_challenge = $this->_dm->getRepository(PVP::class)->find($challenge);
                if ($user->getId() === $instance_challenge->getChallenger()->getId())
                    if (!$instance_challenge->getSubmissionTimeChallenger())
                        $instance_challenge->setSubmissionTimeChallenger(microtime(true));
                if ($user->getId() === $instance_challenge->getChallenged()->getId())
                    if (!$instance_challenge->getSubmissionTimeChallenged())
                        $instance_challenge->setSubmissionTimeChallenged(microtime(true));

                if ($instance_challenge->getSubmissionTimeChallenger() && $instance_challenge->getSubmissionTimeChallenged()){
                    $instance_challenge->setCompleted(true);
                    if ($instance_challenge->getSubmissionTimeChallenger() > $instance_challenge->getSubmissionTimeChallenged())
                        $instance_challenge->setWinner($instance_challenge->getChallenger());
                    else if ($instance_challenge->getSubmissionTimeChallenged() > $instance_challenge->getSubmissionTimeChallenger())
                        $instance_challenge->setWinner($instance_challenge->getChallenged());
                    else
                        $instance_challenge->setWinner(null);
                }
                $this->_dm->persist($instance_challenge);
                $this->_dm->flush();
                    
                return $response->withJson([ 'return' => true,  'message' => 'Sua resposta está correta, veja seu resultado no painel de desafios!', 'user' => $user->toArray()]);
            } else {
                $validateInstanced->saveHistory($params);
                return $response->withJson([ 'return' => true,  'message' => 'A resposta está correta!', 'user' => $user->toArray()]);
            }
        }
        
        return $response->withJson([ 'return' => false,  'message' => 'A resposta está errada! Lembre-se de colocar uma quebra de linha ao imprimir suas respostas.', 'id' => $idActivity ]);
    }

}