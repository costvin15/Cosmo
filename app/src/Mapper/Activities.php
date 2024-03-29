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
     * @ODM\Field(name="inputdescription", type="string")
     */
    private $input_description;

    /**
     * @ODM\Field(name="outputdescription", type="string")
     */
    private $output_description;

    /**
     * @ODM\Field(name="activities", type="hash")
     */
    private $activities;

    /**
     * @ODM\Field(name="example", type="hash")
     */
    private $activity_example;

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
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="activity")
     */
    private $uploader;

    /*
     * @ODM\ReferenceMany(targetDocument="Activities", mappedBy="requirements")
     */
    private $next_activities;

    /**
     * @ODM\ReferenceMany(targetDocument="Activities", mappedBy="next_activities")
     */
    private $requirements;

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

    /**
     * @ODM\Field(name="moedas", type="int")
     */
    private $moedas;

    /**
     * @ODM\Field(name="cust", type="int")
     */
    private $cust = 0;

    /**
     * @ODM\Field(name="xp", type="int")
     */
    private $xp;

    /**
     * @ODM\Field(name="category", type="string")
     */
    private $category;

    public function __construct($params = array())
    {
        $next_activities = new \Doctrine\Common\Collections\ArrayCollection();
        $requirements = new \Doctrine\Common\Collections\ArrayCollection();

        if (count($params) > 0){
            $this->question = $params['shortDesc'];
            $this->fullquestion = $params['questionDesc'];
            $this->title = $params['questionTitle'];
            $this->activities =  array(array("model" => array('in'=> new  \MongoBinData($params['testIn'], \MongoBinData::GENERIC), 'out'=> new \MongoBinData($params['testOut'],\MongoBinData::GENERIC)), "plugin" => "App\\PluginActivities\\Problems\\Mapper\\Config"));
            $this->dateCreate = "2016-11-02T14:07:27.000+0000";
            $this->order = $params['order'];
            $this->group = $params['group'];
        }
    }

    public function toArray() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "question" => $this->question,
            "fullquestion" => $this->fullquestion,
            "input_description" => $this->input_description,
            "output_description" => $this->output_description,
            "activity_example" => $this->getActivityExample(),
            "activities" => $this->getActivities(),
            "tasks" => $this->activities["tasks"],
            "group" => $this->group,
            "order" => $this->order,
            "moedas" => $this->moedas,
            "cust" => $this->cust,
            "xp" => $this->xp,
            "category" => $this->category
        ];
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }


      /**
     * @return mixed
     */
    public function getMoedas()
    {
        return $this->moedas;
    }

     /**
     * @param mixed $moedas
     */
    public function setMoedas($moedas)
    {
        $this->moedas = $moedas;
    }

     /**
     * @return mixed
     */
    public function getCust()
    {
        return $this->cust;
    }

     /**
     * @param mixed $cust
     */
    public function setCust($cust)
    {
        $this->cust = $cust;
    }

     /**
     * @return mixed
     */
    public function getXP()
    {
        return $this->xp;
    }

     /**
     * @param mixed $xp
     */
    public function setXP($xp)
    {
        $this->xp = $xp;
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
    public function setOrder($order)
    {
        $this->order = $order;
    }


    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
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
     */
    public function getInputDescription(){
        return $this->input_description;
    }
    
    /**
     * @param mixed $input_description
     */
    public function setInputDescription($input_description){
        $this->input_description = $input_description;
    }

    /**
     * @return mixed
     */
    public function getOutputDescription(){
        return $this->output_description;
    }
    
    /**
     * @param mixed $output_description
     */
    public function setOutputDescription($output_description){
        $this->output_description = $output_description;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getActivities()
    {
        $nameSpacePlugin = $this->activities['plugin'];

        if (!class_exists($nameSpacePlugin))
            throw new \Exception('Class plugin not found! Classname: ' . $nameSpacePlugin);

        $configClass = new $nameSpacePlugin;
        $arrayConfig = $configClass();
        
        $this->view = $arrayConfig['view'];
        $this->validate = $arrayConfig['validate'];
        $hydrator = new $arrayConfig['hydrator'];

        $tasks = $this->activities["tasks"];
        
        return $hydrator($tasks);
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
    public function getActivityExample(){
        $result = array();
        for ($i = 0; $i < count($this->activity_example); $i++){
            $result[$i] = array();
            foreach ($this->activity_example[$i] as $key => $value){
		$result[$i][$key] = $value->bin;
	    	for ($j = 0; $j < strlen($result[$i][$key]); $j++){
		    //if ($result[$i][$key][$j] == '\n'){
		    	//$result[$i][$key][$j] == '-';
			//substr_replace($result[$i][$key], "<br>", $j);
	 	    //}
		}
	    }
        }

        return $result;
    }

    /**
     * @param mixed $activity_example
     */
    public function setActivityExample($activity_example){
        $this->activity_example = $activity_example;
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

    /**
     * @return User
     */
    public function getUploader(){
        return $this->uploader;
    }

    /**
     * @param mixed $uploader
     */
    public function setUploader($uploader){
        $this->uploader = $uploader;
    }

    public function getNextActivities(){
        return $this->next_activities;
    }

    public function setNextActivities($next_activities){
        $this->next_activities = $next_activities;
    }
    
    public function addNextActivity($next_activity){
        $next_activity->requirements[] = $this;
        $this->next_activities[] = $next_activity;
    }
    
    public function getRequirements(){
        return $this->next_activities;
    }

    public function setRequirements($requirements){
        $this->requirements = $requirements;
    }

    public function addRequirement($requirement){
        $requirement->next_activity[] = $this;
        $this->requirements[] = $requirement;
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
