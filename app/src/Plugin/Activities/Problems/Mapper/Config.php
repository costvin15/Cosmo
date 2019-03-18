<?php

namespace App\Plugin\Activities\Problems\Mapper;


class Config
{
    public function __invoke()
    {
        return [
            'name' => 'Problems',
            'view' => 'Plugin\\Activities\\Problems\\View\\problems.twig',
            'hydrator' => 'App\\Plugin\\Activities\\Problems\\Mapper\\ProblemsHydrator',
            'validate' => 'App\\Plugin\\Activities\\Problems\\Mapper\\ProblemsValidate',
        ];
    }

}