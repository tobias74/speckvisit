<?php 
namespace Speckvisit\Parser;

class SequenceParser extends CompositeParser 
{
    protected $parsers = array();

    public function addToSequence( Parser $parser ) 
    {
        $this->parsers[]= $parser;
        return $this;
    }

    public function getParseResult($scanner)
    {
        if ( empty( $this->parsers ) ) 
        {
            return new ParseFailure();
        }
        else 
        {
            $startState = $scanner->saveToMemento();
            foreach($this->parsers as $parser) 
            {
                if (!$parser->parse($scanner)->wasParsingSuccessful()) 
                {
                    $scanner->restoreFromMemento( $startState );
                    return new ParseFailure();
                }
            }
            return new ParseSuccess();
        }
    }
    
}
