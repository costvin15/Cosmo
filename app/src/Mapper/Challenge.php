<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Challenges")
 */
class Challenge {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Classes", inversedBy="challenges")
     */
    private $class;

    /**
     * @ODM\Field(name="title", type="string")
     */
    private $title;

    /**
     * @ODM\Field(name="opening", type="date")
     */
    private $opening;

    /**
     * @ODM\Field(name="validity", type="date")
     */
    private $validity;

    /**
     * @ODM\Field(name="type", type="string")
     */
    private $type;

    /**
     * @ODM\Field(name="questions", type="hash")
     */
    private $questions;

    /**
     * @ODM\ReferenceMany(targetDocument="ChallengeHistory", mappedBy="challenge")
     */
    private $challengeHistory;

    public function toArray(){
        return array(
            "id" => $this->id,
            "title" => $this->title,
            "opening" => $this->opening,
            "validity" => $this->validity,
            "type" => $this->type,
            "questions" => $this->questions
        );
    }

    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getOpening(){
        return $this->opening;
    }

    public function setOpening($opening){
        $this->opening = $opening;
    }

    public function getValidity(){
        return $this->validity;
    }

    public function setValidity($validity){
        $this->validity = $validity;
    }

    public function getQuestions(){
        return $this->questions;
    }

    public function setQuestions($questions){
        $this->questions = $questions;
    }

    public function getClass(){
        return $this->class;
    }

    public function setClass($class){
        $this->class = $class;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }
}