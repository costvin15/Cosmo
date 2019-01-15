<?php

namespace App\Http\Controller\Validate;

use Zend\Validator\NotEmpty;

class AdministratorActivityControlValidate extends AbstractControllerValidate {
    public function saveActivityAction(Request $request){
        $title = $request->getParam("title");
        $question = $request->getParam("question");
        $fullquestion = $request->getParam("fullquestion");
        $group = $this->_dm->getRepository(GroupActivities::class)->findBy(["name" => $request->getParam("group")]);
        $input_description = $request->getParam("input_description");
        $input_example = $request->getParam("input_example");
        $output_description = $request->getParam("output_description");
        $output_example = $request->getParam("output_example");
        $input = $request->getParam("input");
        $output = $request->getParam("output");

        $is_empty = new NotEmpty();

        if (!$is_empty->isValid($title)){
            $this->error = "O campo Título é inválido.";
            return false;
        } elseif (!$is_empty->isValid($question)){
            $this->error = "O campo Descrição Curta é inválido.";
            return false;
        } elseif (!$is_empty->isValid($fullquestion)){
            $this->error = "O campo Descrição é inválido.";
            return false;
        } elseif (count($group) == 0){
            $this->error = "O campo Categoria é inválido.";
            return false;
        } elseif (!$is_empty->isValid($input_description)){
            $this->error = "O campo Descrição de Entrada é inválido.";
            return false;
        } elseif (!$is_empty->isValid($input_example)){
            $this->error = "O campo Entrada de Exemplo é inválido.";
            return false;
        } elseif (!$is_empty->isValid($output_description)){
            $this->error = "O campo Descrição de Saída é inválido.";
            return false;
        } elseif (!$is_empty->isValid($output_example)){
            $this->error = "O campo Saída de Exemplo é inválido.";
            return false;
        } elseif (!$is_empty->isValid($input)){
            $this->error = "O campo Entrada é inválido.";
            return false;
        } elseif (!$is_empty->isValid($output)){
            $this->error = "O campo Saída é inválido.";
            return false;
        }

        return true;
    }
}