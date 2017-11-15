<?php
namespace drmad\semeele;

/**
 * An XML comment
 */
class Comment extends ChildlessNode
{
    public function getXML()
    {
        return '<!-- ' . $this->encode($this->nodeName) . ' -->';
    }
}
