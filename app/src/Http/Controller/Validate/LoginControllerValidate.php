<?php

namespace App\Http\Controller\Validate;


use Slim\Http\Request;
use Zend\Validator\EmailAddress;

class LoginControllerValidate extends AbstractControllerValidate
{

    public function authenticateAction(Request $request) {

        $is_email = new EmailAddress();

        $username = $request->getParam("username");
        $password = $request->getParam("password");

        if (($username == '') && ($password == '')) {
            $this->error = "O campo Email ou Senha estão vazios.";
            return false;
        }

        if($username == ''){
            $this->error = "O campo Email é obrigatório.";
            return false;
        }

        if($password == ''){
            $this->error = "O campo Senha é obrigatório.";
            return false;
        }

        return true;
    }
}