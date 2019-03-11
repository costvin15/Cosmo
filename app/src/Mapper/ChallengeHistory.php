<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="ChallengeHistory")
 */
class ChallengeHistory {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="challengeActivities")
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument="Challenge", inversedBy="challengeHistory")
     */
    private $challenge;
    
    /**
     * @ODM\Field(name="execution_time_start", type="float")
     */
    private $execution_time_start;

    /**
     * @ODM\Field(name="execution_time_end", type="float")
     */
    private $execution_time_end;
    
    /**
     * @ODM\Field(name="datetime", type="date")
     */
    private $datetime;

    /**
     * @ODM\Field(name="code", type="string")
     */
    private $code;

    /**
     * ODM\Field(name="language", type="string")
     */
    private $language;

    /**
     * @ODM\Field(name="level", type="string")
     */
    private $level;

    /**
     * @ODM\Field(name="type", type="string")
     */
    private $type;

    public function getId(){
        return $this->id;
    }

    public function getUser(){
        return $this->$user;
    }

    public function setUser($user){
        $this->user = $user;
    }

    public function getChallenge(){
        return $this->challenge;
    }

    public function setChallenge($challenge){
        $this->challenge = $challenge;
    }

    public function getExecutionTimeStart(){
        return $this->execution_time_start;
    }

    public function setExecutionTimeStart($execution_time_start){
        $this->execution_time_start = $execution_time_start;
    }

    public function getExecutionTimeEnd(){
        return $this->execution_time_end;
    }

    public function setExecutionTimeEnd($execution_time_end){
        $this->execution_time_end = $execution_time_end;
    }

    public function getDateTime(){
        return $this->datetime;
    }

    public function setDateTime($datetime){
        $this->datetime = $datetime;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getLanguage(){
        return $this->language;
    }

    public function setLanguage($language){
        $this->language = $language;
    }

    public function getLevel(){
        return $this->level;
    }

    public function setLevel($level){
        $this->level = $level;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }
}