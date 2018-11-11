<?php 
namespace Speckvisit\Parser;


class NumberParser extends LeafParser 
{
    function __construct( $number=null ) 
    {
        $this->number = $number;
    }

    protected function getParseResult($scanner)
    {
        if (!$scanner->getCurrentToken()->isNumber()) 
        {
            return new ParseFailure();
        }
        else if (is_null( $this->number ) ) 
        {
            return new ParseSuccess($scanner->getCurrentToken()->getValue());
        } 
        else if ($this->number === $scanner->getCurrentToken()->getValue() )  
        {
            return new ParseSuccess($this->number);
        }
        else
        {
            return new ParseFailure();
        }
    }

}
