<?php

namespace App\Mapper;

use App\Facilitator\App\SessionFacilitator;
use App\Facilitator\Database\DatabaseFacilitator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Activities", repositoryClass="App\Mapper\Repository\ActivitiesRepository")
 */
class Activities
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="question", type="string")
     */
    private $question;

    /**
     * @ODM\Field(name="fullquestion", type="string")
     */
    private $fullquestion;

    /**
     * @ODM\Field(name="title", type="string")
     */
    private $title;

    /**
     * @ODM\Field(name="activities", type="collection")
     */
    private $activities;

    /**
     * @ODM\Field(name="created_at", type="date")
     */
    private $dateCreate;

    /**
     * @ODM\Field(name="order", type="int")
     */
    private $order;

    /**
     * @ODM\ReferenceOne(targetDocument="GroupActivities", inversedBy="activity")
     */
    private $group;

    /**
     * @ODM\ReferenceMany(targetDocument="HistoryActivities", mappedBy="activity")
     */
    private $historyActivities;

    /**
     * @ODM\ReferenceMany(targetDocument="AttemptActivities", mappedBy="activity")
     */
    private $attemptActivities;

    /**
     * @var mixed
     */
    private $view;

    /**
     * @var mixed
     */
    private $validate;


    public function __construct($params)
    {
        $this->question = $params['shortDesc'];
        $this->fullquestion = $params['questionDesc'];
        $this->title = $params['questionTitle'];
        $this->activities =  array(array("model" => array('in'=> new  \MongoBinData($params['testIn'], \MongoBinData::GENERIC), 'out'=> new \MongoBinData($params['testOut'],\MongoBinData::GENERIC)), "plugin" => "App\\PluginActivities\\Problems\\Mapper\\Config"));
        $this->dateCreate = "2016-11-02T14:07:27.000+0000";
        $this->order = $params['order'];
        $this->group = $params['group'];

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getFullquestion()
    {
        return $this->fullquestion;
    }

    /**
     * @param mixed $fullquestion
     */
    public function setFullquestion($fullquestion)
    {
        $this->fullquestion = $fullquestion;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getActivities()
    {
        $nameSpacePlugin = $this->activities[0]['plugin'];

        if (!class_exists($nameSpacePlugin))
            throw new \Exception('Class plugin not found! Classname: ' . $nameSpacePlugin);


        $configClass = new $nameSpacePlugin;
        $arrayConfig = $configClass();
        $this->view = $arrayConfig['view'];
        $this->validate = $arrayConfig['validate'];

        $hydrator = new $arrayConfig['hydrator'];
        return $hydrator($this->activities[0]['model']);
    }

    /**
     * @param mixed $activities
     */
    public function setActivities($activities)
    {
        $this->activities = $activities;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * @param mixed $dateCreate
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;
    }

    public function getView() {
        if ($this->view == null)
            $this->getActivities();

        return $this->view;
    }

    public function getValidate() {
        if ($this->validate == null)
            $this->getActivities();

        return $this->validate;
    }

    /**
     * @return GroupActivities
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


    public function getIn(){
        return $this->activities[0]['model']['in']->bin;
    }

    public function getOut(){
        return $this->activities[0]['model']['out']->bin;
    }

    /**
     * @return mixed
     */
    public function getHistoryActivities()
    {
        return $this->historyActivities;
    }

    /**
     * @param mixed $historyActivities
     */
    public function setHistoryActivities($historyActivities)
    {
        $this->historyActivities = $historyActivities;
    }

    /**
     * @return mixed
     */
    public function getAttemptActivities()
    {
        return $this->attemptActivities;
    }

    /**
     * @param mixed $attemptActivities
     */
    public function setAttemptActivities($attemptActivities)
    {
        $this->attemptActivities = $attemptActivities;
    }

}