<?php

namespace App\Plugin\Activities\Problems\Mapper;

use MongoDB\BSON\Binary;

class Problems
{

    /**
     * @var string
     */
    private $tasks;

    /**
     * Problems constructor.
     * @param array $tasks
     */
    public function __construct(array $tasks){
        $this->tasks = $tasks;
    }

    /**
     * @return mixed
     */
    public function getTasks(){
        return $this->tasks;
    }

    /**
     * @param array $tasks;
     */
    public function setTasks($tasks){
        $this->tasks = $tasks;
    }
}