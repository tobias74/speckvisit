<?php 
namespace Speckvisit\Parser;


class AlternationParser extends CompositeParser {

    protected $parsers = array();

    public function addAlternative( Parser $parser ) 
    {
        $this->parsers[]= $parser;
        return $this;
    }
    
    public function getParseResult($scanner) 
    {
        $startState = $scanner->saveToMemento();
        foreach ( $this->parsers as $parser ) 
        {
            if ( !$scanner->isAtEndOfFile() && $parser->parse($scanner)->wasParsingSuccessful() ) 
            {
                return new ParseSuccess();
            }
        }
        $scanner->restoreFromMemento( $startState );
        return new ParseFailure();
    }


}
