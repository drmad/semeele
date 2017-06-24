<?php
namespace drmad\semeele;

/**
 * CDATA value 
 */
class Cdata
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return '<![CDATA[' . $this->string . ']]>';
    }
}