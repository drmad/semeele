<?php 
namespace drmad\semeele;

/**
 * Abstract class to define nodes without children.
 */
abstract class ChildlessNode extends Node
{
    public function child(...$params)
    {
        throw new \BadMethodCallException("This node can't have children.");
    }
}