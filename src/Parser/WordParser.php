<?php 
namespace Speckvisit\Parser;

class WordParser extends LeafParser 
{
    function __construct( $word=null ) 
    {
        $this->word = $word;
    }

    protected function getParseResult($scanner)
    {
        if ( !$scanner->getCurrentToken()->isWord() ) 
        {
            return new ParseFailure();
        }
        else if (is_null($this->word))
        {
            return new ParseSuccess($scanner->getCurrentToken()->getValue());
        } 
        else if ($this->word === $scanner->getCurrentToken()->getValue() ) 
        {
            return new ParseSuccess($this->word);
        }
        else
        {
            return new ParseFailure();
        }
    }
}
