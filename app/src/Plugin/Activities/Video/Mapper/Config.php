<?php

namespace App\Plugin\Activities\Video\Mapper;


class Config
{
    public function __invoke()
    {
        return [
            "name" => "Video",
            'view' => 'Plugin/Activities/Video/View/video.twig',
            'hydrator' => 'App\\Plugin\\Activities\\Video\\Mapper\\VideoHydrator',
            'validate' => '',
        ];
    }

}