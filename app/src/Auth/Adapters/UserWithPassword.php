<?php

namespace App\Auth\Adapters;

use App\Facilitator\App\ContainerFacilitator;
use App\Facilitator\App\JWTFacilitator;
use App\Facilitator\Database\DatabaseFacilitator;
use App\Mapper\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use SlimAuth\AuthAdapterInterface;
use SlimAuth\AuthResponse;
use App\Facilitator\App\SessionFacilitator;
use App\Auth\Adapters\UserWithPassword;
use SlimAuth\SlimAuthFacade;

class UserWithPassword implements AuthAdapterInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var DocumentManager
     */
    private $databaseConnection;

    private $is_update;

    /**
     * AuthAdapterUser constructor.
     *
     * @param $username string
     * @param $password string
     */
    public function __construct(string $username, string $password = "", bool $is_update = false){
        $this->username = $username;
        $this->password = $password;
        $this->is_update = $is_update;
        $this->databaseConnection = DatabaseFacilitator::getConnection();
    }

    function authenticate() : AuthResponse
    {
        $ci = ContainerFacilitator::getContainer();

        if (filter_var($this->username, FILTER_VALIDATE_EMAIL))
            if ($this->is_update)
                $arrayUser = $this->databaseConnection->getRepository(User::class)->findBy(array("username" => $this->username));
            else
                $arrayUser = $this->databaseConnection->getRepository(User::class)->getUserWithUsernamePassword($this->username, $this->password);
        else
            if ($this->is_update)
                $arrayUser = $this->databaseConnection->getRepository(User::class)->findBy(array("nickname" => $this->username));
            else
                $arrayUser = $this->databaseConnection->getRepository(User::class)->getUserWithNicknamePassword($this->username, $this->password);

        if (count($arrayUser) == 0)
            return new AuthResponse(AuthResponse::AUTHRESPONSE_FAILURE, 'User not found');

        $user = $arrayUser[0];

        $arraySettings = $ci->get('settings');
        return new AuthResponse(AuthResponse::AUTHRESPONSE_SUCCESS, 'User auth success',
            $arraySettings['session']['name'], $user->toArray());
    }

    public static function updateSession($username){
        $adapter = new UserWithPassword($username, "", true);
        $update_user = new SlimAuthFacade($adapter, SessionFacilitator::getSession());
        $update_user->auth();
    }
}