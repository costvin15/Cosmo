<?php

namespace App\Mapper;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use App\Model\Util\ImageBase64;
use App\Facilitator\App\ContainerFacilitator;

/**
 * @ODM\Document(collection="User", repositoryClass="App\Mapper\Repository\UserRepository")
 */
class User
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    private $id;

    /**
     * @ODM\Field(name="username", type="string")
     */
    private $username;

    /**
     * @ODM\Field(name="password", type="string")
     */
    private $password;

    /**
     * @ODM\Field(name="fullname", type="string")
     */
    private $fullname;

    /**
     * @ODM\Field(name="administrator", type="boolean")
     */
    private $administrator;

    /**
     * @ODM\Field(name="blocked", type="hash")
     */
    private $blocked;

    /**
     * @ODM\Field(name="interface", type="string")
     */
    private $interface;

    /**
     * @ODM\ReferenceMany(targetDocument="HistoryActivities", mappedBy="user")
     */
    private $historyActivities;

    /**
     * @ODM\ReferenceMany(targetDocument="AttemptActivities", mappedBy="user")
     */
    private $attemptActivities;

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $password
     * @param $fullname
     * @param $administrator
     */
    public function __construct($id = null, $username = null, $password = null, $fullname = null, $administrator = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->fullname = $fullname;
        $this->administrator = $administrator;
        $this->historyActivities = [];
    }

    public function getAvatar(){
        $image_base_64 = new ImageBase64();
        $path = ContainerFacilitator::getContainer()->get("settings")->get("storage.photo");
        $filename = "";
        
        if (file_exists($path . DIRECTORY_SEPARATOR . $this->id))
            $filename = $path . DIRECTORY_SEPARATOR . $this->id;
        else
            $filename = $path . DIRECTORY_SEPARATOR . "default.png";
        
        return $image_base_64->castPathFile($filename);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'password' => $this->password,
            'fullname' => $this->fullname,
            'administrator' => $this->administrator,
            'blocked' => $this->blocked,
            'avatar' => $this->getAvatar(),
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

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    /**
     * @return mixed
     */
    public function getHistoryActivities()
    {
        return $this->historyActivities;
    }

    /**
     * @param mixed $historyActivities
     */
    public function setHistoryActivities($historyActivities)
    {
        $this->historyActivities = $historyActivities;
    }

    /**
     * @return mixed
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * @param mixed $administrator
     */
    public function setAdministrator($administrator)
    {
        $this->administrator = $administrator;
    }
    
    /**
     * @return mixed
     */
    public function getBlocked(){
        return $this->blocked;
    }

    /**
     * @param array $blocked
     */
    public function setBlocked($blocked){
        $this->blocked = $blocked;
    }

    /**
     * @return mixed
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @param mixed $interface
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
    }

    /**
     * @return mixed
     */
    public function getAttemptActivities()
    {
        return $this->attemptActivities;
    }

    /**
     * @param mixed $attemptActivities
     */
    public function setAttemptActivities($attemptActivities)
    {
        $this->attemptActivities = $attemptActivities;
    }
}