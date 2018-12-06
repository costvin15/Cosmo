<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 26/10/16
 * Time: 09:18
 */

namespace App\PluginActivities\Video\Mapper;


class VideoHydrator
{
    public function __invoke(array $model)
    {
        return new Video($model['url']);
    }
}