<?php

namespace Drmad\Semeele;

/**
 * XML root node representation which include a prolog tag
 */
class Document extends Node
{
    /** XML declaration node, as a processing instruction */
    private $declaration;

    public function __construct(
        $rootNodeName,
        $version = '1.0',
        $encoding = 'UTF-8'
    )
    {
        $this->declaration = new ProcessingInstruction('xml', null, compact('version', 'encoding'));

        parent::__construct($rootNodeName, null, [], $encoding);
    }

    /**
     * Returns the XML declaration node, so you can add attributes
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    public function getXML()
    {
        return $this->declaration->getXML() . parent::getXML();
    }
}
