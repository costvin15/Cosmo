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
     * @ODM\Field(name="nickname", type="string")
     */
    private $nickname;

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
     * @ODM\Field(name="fulltitle", type="string")
     */
    private $fulltitle;
        
    /**
     * @ODM\Field(name="answered_activities", type="int")
     */
    private $answered_activities = 0;

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
     * @param $fulltitle
     * @param $administrator
     */
    public function __construct($id = null, $username = null, $password = null, $fullname = null, $administrator = false, $fulltitle = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->fullname = $fullname;
        $this->fulltitle = $fulltitle;
        $this->administrator = $administrator;
        $this->historyActivities = [];
        $this->answered_activities = 0;
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
            'nickname' => $this->nickname,
            'username' => $this->username,
            'password' => $this->password,
            'fullname' => $this->fullname,
            'fulltitle' => $this->fulltitle,
            'administrator' => $this->administrator,
            'blocked' => $this->blocked,
            'avatar' => $this->getAvatar(),
            'answered_activities' => $this->answered_activities,
        ];
    }

    public function toArrayMinified(){
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'fulltitle' => $this->fulltitle,
            'answered_activities' => $this->answered_activities,
        ];
    }

    public function toRankingArray(){
        return [
            "nickname" => $this->nickname ? $this->nickname : $this->username,
            "points" => $this->answered_activities,
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
    public function getNickname(){
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname){
        $this->nickname = $nickname;
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
    public function getFulltitle()
    {
        return $this->fulltitle;
    }

    /**
     * @param mixed $fulltitle
     */
    public function setFulltitle($fulltitle)
    {
        $this->fulltitle = $fulltitle;
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
    public function getAnsweredActivities(){
        return $this->answered_activities;
    }

    /**
     * @param mixed $answered_activities
     */
    public function setAnsweredActivities($answered_activities){
        $this->answered_activities = $answered_activities;
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