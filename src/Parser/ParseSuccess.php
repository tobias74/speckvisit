<?php 
namespace Speckvisit\Parser;

class ParseSuccess extends ParseResult
{
    public function __construct($value=null)
    {
        $this->value = $value;
    }
    
    public function wasParsingSuccessful()
    {
        return true;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
}
