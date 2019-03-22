<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Star", repositoryClass="App\Mapper\Repository\StarRepository")
 */
class Star
{

    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="Star")
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument="GroupActivities", inversedBy="Star")
     */
    private $groupActivities;

    /**
     * @ODM\ReferenceOne(targetDocument="CategoryActivities")
     */
    private $categoryActivities;

    /**
     * @ODM\Field(name="time_start", type="float")
     */
    private $timeStart;

    /**
     * @ODM\Field(name="time_end", type="float")
     */
    private $timeEnd;

    /**
     * @ODM\Field(name="completed", type="bool")
     */
    private $completed;

    function __construct() {

        $this->completed = false;

    }
    public function toArray(){
        return array(
            "id" => $this->id,
            "user" => $this->user->toArrayMinified(),
            "group" => $this->groupActivities->toArray(),
            "time_start" => $this->timeStart,
            "time_end" => $this->timeEnd,
            "completed" => $this->completed,
        );
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getGroupActivities()
    {
        return $this->groupActivities;
    }

    /**
     * @param mixed $groupActivities
     */
    public function setGroupActivities($groupActivities)
    {
        $this->groupActivities = $groupActivities;
    }

    /**
     * @return mixed
     */
    public function getCategoryActivities()
    {
        return $this->categoryActivities;
    }

    /**
     * @param mixed $groupActivities
     */
    public function setCategoryActivities($categoryActivities)
    {
        $this->categoryActivities = $categoryActivities;
    }

    /**
     * @return mixed
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * @param mixed $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * @param mixed $timeEnd
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = $timeEnd;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }
}