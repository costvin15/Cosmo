<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 22/04/17
 * Time: 21:25
 */

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Log", repositoryClass="App\Mapper\Repository\LogRepository")
 */
class Log
{

    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ODM\Field(name="level", type="string")
     */
    private $level;

    /**
     * @var
     * @ODM\Field(name="message", type="string")
     */
    private $message;

    /**
     * @var
     * @ODM\Field(name="id_user", type="string")
     */
    private $id_user;

    /**
     * @var
     * @ODM\Field(name="datetime", type="date")
     */
    private $date;

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
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

}