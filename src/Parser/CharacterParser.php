<?php 
namespace Speckvisit\Parser;


class CharacterParser extends LeafParser {
    protected $character;

    function __construct( $character=null ) 
    {
        $this->character = $character;
    }
    
    protected function getParseResult($scanner)
    {
        if ( !$scanner->getCurrentToken()->isCharacter() )
        {
            return new ParseFailure();
        }
        else if (is_null( $this->character ) ) 
        {
            return new ParseSuccess($scanner->getCurrentToken()->getValue());
        } 
        else if ( $scanner->getCurrentToken()->getValue() === $this->character )
        {
            return new ParseSuccess($this->character);
        }
        else
        {
            return new ParseFailure();
        }
    }

}
