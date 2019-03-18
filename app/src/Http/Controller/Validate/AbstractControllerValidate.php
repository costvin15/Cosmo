<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 12/09/17
 * Time: 21:53
 */

namespace App\Http\Controller\Validate;


abstract class AbstractControllerValidate
{

    protected $error = '';

    public function getError() {
        return $this->error;
    }

}