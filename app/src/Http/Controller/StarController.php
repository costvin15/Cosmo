<?php

namespace App\Http\Controller;

use Interop\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Model\Category\InterfaceCategory;
use App\Model\Category\CategoryRequired;
use App\Model\Category\CategoryAgainstTime;
use App\Facilitator\App\SessionFacilitator;
use App\Mapper\User;
use App\Mapper\GroupActivities;
use App\Mapper\CategoryActivities;
use App\Mapper\Star;
use App\Mapper\HistoryActivities;


/**
 * Class AdministratorControlController
 * @package App\Http\Controller
 * @Controller
 * @Route("/star")
 */
class StarController extends AbstractController
{
    /**
     * StarController constructor.
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
     * @Get(name="/check/{id_group}/{id_category}", middleware={"App\Http\Middleware\SessionMiddleware"}, alias="star.check")
     */
    public function checkAction(Request $request, Response $response, Array $args) {
        $idGroup = $args["id_group"];        
        $idCategory = $args["id_category"];

        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);

        $category = $this->_dm->getRepository(CategoryActivities::class)->find($idCategory);
        $group = $this->_dm->getRepository(GroupActivities::class)->find($idGroup);
        $star = $this->_dm->getRepository(Star::class)->findStar($user,$group,$category);
        $history = $this->_dm->getRepository(HistoryActivities::class)->findHistories($user,$group);
        $optional = [];

        if($category->getCategory() == InterfaceCategory::REQUIRED){
            $checker = new CategoryRequired();
        }
        if($category->getCategory() == InterfaceCategory::AGAINST_TIME){
            $optional = ["time"=>$star->getTimeEnd()-$star->getTimeStart()];
            $checker = new CategoryAgainstTime();
        }

        if($checker->check($history,$star,$user)){
            $star->setCompleted(true);
            $star->setTimeEnd(time());
            $this->_dm->persist($star);
            $this->_dm->flush();
            return $response->withJson(array_merge([ 'return' => true,  'message' => 'A resposta está correta!',"star"=>$category->getCategory()],$optional));
        }

        return $response->withJson([ 'return' => true,  'message' => 'A resposta está correta!']);;
    }
}