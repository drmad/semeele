<?php
namespace drmad\semeele;

/**
 * XML node representation
 */
class Node
{
    private $parent;
    private $children = [];
    
    private $nodeName;
    private $attributes = [];
    private $content;

    /**
     * Constructor
     */
    public function __construct($nodeName, $content = null, $attributes = [])
    {
        if (is_array($content)) {
            $attributes = $content;
            $content = null;
        }

        $this->nodeName = $nodeName;
        $this->content = $content;
        $this->attributes = $attributes;
    }

    /**
     * Adds a child, returns the newly created node
     */
    public function child(...$params) 
    {
        return $this->children[] = (new self(...$params))->setParent($this);
    }

    /**
     * Adds a child, returns this (parent) node.
     */
    public function add(...$params)
    {
        $this->child(...$params);
        return $this;
    }

    /**
     * Returns the parent node.
     */
    public function parent() 
    {
        return $this->parent;
    }

    /**
     * Sets a parent node. Used for maintain the chain in new 
     * inserted nodes.
     */
    protected function setParent(Node $parent) 
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Properly encode a string for including in the XML
     *
     * FIX: Is necessary some kind of encoding?
     *
     */
    private function encode($string)
    {
        return $string;
    }

    /**
     * Returns the XML representation of this Node tree
     *
     * @return string XML representation
     */
    public function getXML()
    {
        // Can't have $children and $content
        if ($this->children && $this->content) {
            throw new \RuntimeException("A node can't have both children and content.");
        }

        $has_content = $this->children || $this->content;
        
        $tagname = $this->nodeName;
        if ($this->attributes) {
            
            $attrs = [];
            foreach ($this->attributes as $name => $value) {
                $attr[] = $name . '="' . $value .'"';
            }
            $tagname .= ' ' . join(' ', $attr);
        }

        if ($has_content) {
            $xml = '<' . $tagname . '>';

            if ($this->content) {
                $xml .= $this->encode($this->content);
            }
            if ($this->children) {
                foreach ($this->children as $child) {
                    $xml .= $child->getXML();
                }
            }

            $xml .= '</' . $this->nodeName . '>';
        } else {
            $xml = '<' . $tagname . '/>';
        }

        return $xml;
    }
}
