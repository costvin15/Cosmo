<?php
/**
 * User: dilsonrabelo
 * Date: 26/08/16
 * Time: 20:40
 */

namespace App\Http\Controller;


use App\Facilitator\App\SessionFacilitator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Interop\Container\ContainerInterface;
use RKA\Session;
use Slim\Views\Twig;

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