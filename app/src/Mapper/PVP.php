<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="PVP")
 */
class PVP {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Activities", inversedBy="pvp")
     */
    private $activity;

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="challenges_created")
     */
    private $challenger;

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="challenges_suffered")
     */
    private $challenged;

    /**
     * @ODM\Field(name="completed", type="boolean")
     */
    private $completed;

    /**
     * @ODM\Field(name="start_time_challenger", type="float")
     */
    private $start_time_challenger;
    
    /**
     * @ODM\Field(name="submission_time_challenger", type="float")
     */
    private $submission_time_challenger;
    
    /**
     * @ODM\Field(name="start_time_challenged", type="float")
     */
    private $start_time_challenged;
    
    /**
     * @ODM\Field(name="submission_time_challenged", type="float")
     */
    private $submission_time_challenged;

    public function __construct(Activities $activity, User $challenger, User $challenged){
        $this->activity = $activity;
        $this->challenger = $challenger;
        $this->challenged = $challenged;
        $this->completed = false;
        $this->start_time_challenger = null;
        $this->submission_time_challenger = null;
        $this->start_time_challenged = null;
        $this->submission_time_challenged = null;
    }

    public function toArray(){
        return array(
            "id" => $this->id,
            "activity" => $this->activity,
            "challenger" => $this->challenger,
            "challenged" => $this->challenged,
            "start_time_challenger" => $this->start_time_challenger,
            "submission_time_challenger" => $this->submission_time_challenger,
            "start_time_challenged" => $this->start_time_challenged,
            "submission_time_challenged" => $this->submission_time_challenged
        );
    }

    public function getId(){
        return $this->id;
    }

    public function getActivity(){
        return $this->activity;
    }

    public function setActivity(Activities $activity){
        $this->activity = $activity;
    }

    public function getChallenger(){
        return $this->challenger;
    }

    public function setChallenger(array $challenger){
        $this->challenger = $challenger;
    }

    public function getChallenged(){
        return $this->challenged;
    }

    public function setChallenged(array $challenged){
        $this->challenged = $challenged;
    }

    public function getCompleted(){
        return $this->completed;
    }

    public function setCompleted(bool $completed){
        $this->completed = $completed;
    }

    public function getStartTimeChallenger(){
        return $this->start_time_challenger;
    }

    public function setStartTimeChallenger($start_time_challenger){
        $this->start_time_challenger = $start_time_challenger;
    }
    
    public function getSubmissionTimeChallenger(){
        return $this->submission_time_challenger;
    }

    public function setSubmissionTimeChallenger($submission_time_challenger){
        $this->submission_time_challenger = $submission_time_challenger;
    }

    
    public function getStartTimeChallenged(){
        return $this->start_time_challenged;
    }

    public function setStartTimeChallenged($start_time_challenged){
        $this->start_time_challenged = $start_time_challenged;
    }
    
    public function getSubmissionTimeChallenged(){
        return $this->submission_time_challenged;
    }

    public function setSubmissionTimeChallenged($submission_time_challenged){
        $this->submission_time_challenged = $submission_time_challenged;
    }
}