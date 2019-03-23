<?php


namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Badges")
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
     * @ODM\Field(name="level", type="number")
     */
    private $level;

     /**
     * @ODM\ReferenceMany(targetDocument="User", mappedBy="achievements")
     */
    private $users;
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
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getType(){
        return $this->type;
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