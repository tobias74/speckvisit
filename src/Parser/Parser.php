<?php 
namespace Speckvisit\Parser;

abstract class Parser 
{
    protected $discard = false;

    public function setHandler( $handler ) 
    {
        $this->handler = $handler;
        return $this;
    }

    public function discard() 
    {
        $this->discard = true;
        return $this;
    }

}

