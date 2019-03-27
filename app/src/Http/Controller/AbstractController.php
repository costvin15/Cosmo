<?php
/**
 * User: dilsonrabelo
 * Date: 26/08/16
 * Time: 20:40
 */

namespace App\Http\Controller;


use App\Facilitator\App\SessionFacilitator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Container\ContainerInterface;
use RKA\Session;
use Slim\Views\Twig;
use App\Mapper\PVP;
use App\Mapper\User;

abstract class AbstractController
{
    /**
     * @var DocumentManager
     */
    protected $_dm;

    /**
     * @var ContainerInterface $_ci
     */
    protected $_ci;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var array
     */
    protected $attributeView;

    /**
     * AbstractAction constructor.
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->_ci = $ci;
        $this->_dm = $ci->get('database');
        $this->session = $this->_ci->get('session');
        $this->view = $this->_ci->get('view');
        $arraySession = SessionFacilitator::getAttributeSession();
        $this->setAttributeView('attributes', $arraySession);

        $attributes = SessionFacilitator::getAttributeSession();
        $user = $this->_dm->getRepository(User::class)->find($attributes["id"]);

        if ($user){
            $pvp_query = $this->_dm->createQueryBuilder(PVP::class)
                ->field("challenged")->references($user)->getQuery()->execute();
            $this->setAttributeView("pvps", $pvp_query);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setAttributeView($key = '', $value = '') {
        $this->attributeView[$key] = $value;
    }

    /**
     * @return array
     */
    public function getAttributeView() {
        return $this->attributeView;
    }


}