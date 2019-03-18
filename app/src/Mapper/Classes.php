<?php

namespace App\Mapper;

use App\Facilitator\App\SessionFacilitator;
use App\Facilitator\Database\DatabaseFacilitator;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Classes")
 */
class Classes {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="title", type="string")
     */
    private $title;

    /**
     * @ODM\Field(name="code", type="string")
     */
    private $code;

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="classes")
     */
    private $administrator;

    /**
     * @ODM\ReferenceMany(targetDocument="User", mappedBy="class")
     */
    private $students;

    /**
     * @ODM\ReferenceMany(targetDocument="GroupActivities", mappedBy="class")
     */
    private $groups;

    public function toArray(){
        return array(
            "id" => $this->id,
            "title" => $this->title,
            "code" => $this->code,
            "administrator" => $this->administrator->toArrayMinified(),
            "students" => $this->students,
            "groups" => $this->groups
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

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getAdministrator(){
        return $this->administrator;
    }

    public function setAdministrator($administrator){
        $this->administrator = $administrator;
    }

    public function getStudents(){
        return $this->students;
    }

    public function getGroups(){
        return $this->groups;
    }
}