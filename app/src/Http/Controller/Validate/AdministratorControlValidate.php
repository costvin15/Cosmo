<?php

namespace App\Http\Controller\Validate;


use Slim\Http\Request;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class AdministratorControlValidate extends AbstractControllerValidate
{

    public function saveUserAction(Request $request) {

        $username = $request->getParam("username");
        $password = $request->getParam("password");
        $fullname = $request->getParam("fullname");
        $avatar = $request->getParam("avatar");

        $is_email = new EmailAddress();
        $is_empty = new NotEmpty();

        if(!$is_email->isValid($username)){
            $this->error = "O campo Email é Inválido.";
            return false;
        }

        if (!$is_empty->isValid($username)) {
            $this->error = "O campo Email é obrigatório.";
            return false;
        }

        if(!$is_empty->isValid($password)){
            $this->error = "O campo Senha é obrigatório.";
            return false;
        }

        if(!$is_empty->isValid($fullname)){
            $this->error = "O campo Nome Completo é obrigatório.";
            return false;
        }

        if (!$is_empty->isValid($avatar)) {
            $this->error = "O campo Imagem é obrigatório.";
            return false;
        }

        return true;

    }

    public function updateUserAction(Request $request) {

        $fullname = $request->getParam("fullname");
        $avatar = $request->getParam("avatar");

        $is_empty = new NotEmpty();


        if(!$is_empty->isValid($fullname)){
            $this->error = "O campo Nome Completo é obrigatório.";
            return false;
        }

        if (!$is_empty->isValid($avatar)) {
            $this->error = "O campo Imagem é obrigatório.";
            return false;
        }

        return true;
    }
}