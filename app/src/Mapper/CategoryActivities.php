<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="CategoryActivities", repositoryClass="App\Mapper\Repository\CategoryActivitiesRepository")
 */
class CategoryActivities {
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="category", type="string")
     */
    private $category;

    /**
     * @ODM\Field(name="description", type="string")
     */
    private $description;


    /**
     * CategoryActivities constructor.
     */
    public function __construct()
    {
        $category = "Obrigatória";
    }

    public function toArray(){
        return [
            "id" => $this->id,
            "category" => $this->category,
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

    public function setCategory($category) {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    

}