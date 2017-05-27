<?php
namespace drmad\semeele;

/**
 * XML Root document node representation
 */
class Document extends Node
{
    private $version;
    private $encoding;

    public function __construct($rootNodeName, $version = '1.0', $encoding = 'utf-8')
    {
        $this->version = $version;
        $this->encoding = $encoding;
        parent::__construct($rootNodeName);
    }

    public function getXML()
    {
        // First draw the XML declaration
        $xml = '<?xml version="' . $this->version . '" encoding="' . $this->encoding . '"?>' ;

        // FIX: Is obligatory the \n?
        $xml .= "\n";

        return $xml . parent::getXML();
    }
}