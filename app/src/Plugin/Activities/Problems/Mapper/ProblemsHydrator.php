<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 26/10/16
 * Time: 09:18
 */

namespace App\Plugin\Activities\Problems\Mapper;


class ProblemsHydrator
{
    public function __invoke(array $tasks)
    {
        return new Problems($tasks);
    }
}