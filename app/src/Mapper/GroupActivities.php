<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="GroupActivities", repositoryClass="App\Mapper\Repository\GroupActivitiesRepository")
 */
class GroupActivities {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="name", type="string")
     */
    private $name;

    /**
     * @ODM\Field(name="visible", type="boolean")
     */
    private $visible;

    /**
     * @ODM\Field(name="tags", type="hash")
     */
    private $tags = array();

    /**
     * @ODM\ReferenceMany(targetDocument="Activities", mappedBy="group")
     */
    private $activity;

    /**
     * @ODM\ReferenceOne(targetDocument="Classes", inversedBy="groups")
     */
    private $class;

    /**
     * GroupActivities constructor.
     */
    public function __construct()
    {
        $this->activity = [];
    }

    public function toArray(){
        return [
            "id" => $this->id,
            "title" => $this->name,
            "visible" => $this->visible,
            "tags" => $this->tags,
            "activities" => $this->activity,
            "class" => $this->class
        ];
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

    public function setTags($tags) {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param mixed $activity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }
    
    /**
     * @return mixed
     */
    public function getClass(){
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class){
        $this->class = $class;
    }
}