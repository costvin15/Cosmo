<?php


namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Achievements")
 */
class Achievements{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="type", type="string")
     */
    private $type;

    /**
     * @ODM\Field(name="name", type="string")
     */
    private $name;

    /**
     * @ODM\Field(name="level", type="int")
     */
    private $level;

    
    /**
     * Achievements constructor.
     * @param $id
     * @param $type
     * @param $name
     * @param $level
     */
    public function __construct($type = null, $name = null, $level = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->level = 0;
    }
    public function toArray(){
        return array(
            "id" => $this->id,
            "type" => $this->type,
            "name" => $this->name,
            "level" => $this->level
        );
    }

    public function getId(){
        return $this->id;
    }
    public function setId($id){
        $this->$id = $id;
    }


    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->$type = $type;
    }


    public function setName($name){
        $this->$name = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function getLevel(){
        return $this->level;
    }
    
    public function setLevel($level){
        $this->level = $level;
    }

}

?>