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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Validator;

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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @Post(name="/", alias="activities")
     */
    public function activitiesAction(ServerRequestInterface $request, ResponseInterface $response) {

        $idActivity = $request->getParam("id-activities");
        $this->activity = $this->_dm->getRepository(Activities::class)->find($idActivity);

        $this->activity->getActivities();

        $attributes = SessionFacilitator::getAttributeSession();
        return $this->view->render($response, $this->activity->getView(), [ 'attributes' => $attributes, 'activity' => $this->activity ]);
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return mixed
     * @Get(name="/{id}", alias="activities.view")
     */
    public function viewAnswerAction(ServerRequestInterface $request, ResponseInterface $response, array $args){
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
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     * @Post(name="/submit", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="submitactivity")
     */
    public function submitActivityAction(ServerRequestInterface $request, ResponseInterface $response) {
        $params = $request->getParsedBody();
        $idActivity = $params['id_activity'];

        $activity = $this->_dm->getRepository(Activities::class)->find($idActivity);
        $validateClass = $activity->getValidate();

        $validateInstanced = new $validateClass();
        $returnValidate = $validateInstanced($params);
        
        $params["dateini"] = $returnValidate->timeIn;
        $params["datefim"] = $returnValidate->timeOut;

        $validateInstanced->saveAttempt($params, $returnValidate);

        if ($returnValidate->answer) {
            $validateInstanced->saveHistory($params);
            return $response->withJson([ 'return' => true,  'message' => 'A resposta está correta!']);
        }
        return $response->withJson([ 'return' => false,  'message' => 'A resposta está errada!', 'id' => $idActivity ]);
    }

}