<?php

namespace App\Http\Controller\Validate;


use Slim\Http\Request;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class RegisterControllerValidate extends AbstractControllerValidate
{

    public function saveAction(Request $request) {

        $username = $request->getParam("username");
        $password = $request->getParam("password");
        $fullname = $request->getParam("fullname");
        $class = $request->getParam("class");
        $fulltitle = $request->getParam("fulltitle");

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

        return true;

    }

    public function searchAction(Request $request) {

        $username = $request->getQueryParam("username");

        $is_email = new EmailAddress();
        $is_empty = new NotEmpty();

        if(!$is_email->isValid($username)){
            $this->error = "O campo Email é inválido.";
            return false;
        }

        if (!$is_empty->isValid($username)) {
            $this->error = "O campo Email é obrigatório.";
            return false;
        }

        return true;

    }

    public function sendRescuePasswordAction(Request $request) {
        $username = $request->getParam("username");

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

        return true;
    }

}