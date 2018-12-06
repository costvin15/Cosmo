<?php

namespace App\PluginActivities\Video\Mapper;


class Config
{
    public function __invoke()
    {
        return [
            'view' => 'PluginActivities/Video/View/video.twig',
            'hydrator' => 'App\\PluginActivities\\Video\\Mapper\\VideoHydrator',
            'validate' => '',
        ];
    }

}