<?php 
namespace Speckvisit\Parser;

class RepetitionParser extends CompositeParser 
{
    private $parser;
    private $minimumRepetitions;
    private $maximumRepetitions;

    function __construct($parser, $minimumRepetitions=0, $maximumRepetitions=0) 
    {
        $this->parser = $parser;
        $this->minimumRepetitions = $minimumRepetitions;
        $this->maximumRepetitions = $maximumRepetitions;
    }

    public function getParseResult($scanner) 
    {
        $startMemento = $scanner->saveToMemento();
        $parser = $this->parser;
        $count = 0;

        while (true) 
        {
            if ( ($this->maximumRepetitions > 0) && ($count >= $this->maximumRepetitions) ) 
            {
                return new ParseSuccess();
            }
            

            if (!$parser->parse( $scanner )->wasParsingSuccessful()) 
            {
                if ( $this->minimumRepetitions == 0 || $count >= $this->minimumRepetitions ) 
                {
                    return new ParseSuccess();
                }
                else 
                {
                    $scanner->restoreFromMemento( $startMemento );
                    return new ParseFailure();
                }
            }

            $count++;
        }

        return new ParseSuccess();

    }

}
