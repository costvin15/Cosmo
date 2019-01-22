<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="HistoryActivities", repositoryClass="App\Mapper\Repository\HistoryActivitiesRepository")
 */
class HistoryActivities
{

    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="historyActivities")
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument="Activities", inversedBy="historyActivities")
     */
    private $activity;

    /**
     * @ODM\Field(name="time_start", type="float")
     */
    private $timeStart;

    /**
     * @ODM\Field(name="time_end", type="float")
     */
    private $timeEnd;

    /**
     * @ODM\Field(name="code", type="string")
     */
    private $code;

    /**
     * @ODM\Field(name="difficulty", type="string")
     */
    private $difficulty;

    /**
     * @ODM\Field(name="classification", type="string")
     */
    private $classification;

    /**
     * @ODM\Field(name="language", type="string")
     */
    private $language;

    public function toArray(){
        return array(
            "id" => $this->id,
            "user" => $this->user->toArray(),
            "activity" => $this->activity->toArray(),
            "time" => $this->timeEnd - $this->timeStart,
            "code" => $this->code,
            "language" => $this->language,
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * @param mixed $difficulty
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    /**
     * @return mixed
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * @param mixed $classification
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
    }

    /**
     * @return mixed
     */
    public function getLanguage(){
        return $this->language;
    }

    /**
     * @param mixed $language
     */
    public function setLanguage($language){
        $this->language = $language;
    }
}