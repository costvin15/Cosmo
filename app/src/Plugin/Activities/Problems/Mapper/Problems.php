<?php

namespace App\Plugin\Activities\Problems\Mapper;

use MongoDB\BSON\Binary;

class Problems
{

    /**
     * @var string
     */
    private $in;

    /**
     * @var string
     */
    private $out;

    /**
     * Problems constructor.
     * @param string $in
     * @param string $out
     */
    public function __construct($in, $out)
    {
        $this->in = $in->bin;
        $this->out = $out->bin;
    }

    /**
     * @return mixed
     */
    public function getIn()
    {
        return $this->in;
    }

    /**
     * @param mixed $in
     */
    public function setIn($in)
    {
        $this->in = $in;
    }

    /**
     * @return mixed
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * @param mixed $out
     */
    public function setOut($out)
    {
        $this->out = $out;
    }


}