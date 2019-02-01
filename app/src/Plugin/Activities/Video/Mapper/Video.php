<?php

namespace App\Plugin\Activities\Video\Mapper;

class Video
{

    /**
     * @var string
     */
    private $url;

    /**
     * Video constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

}