<?php 
namespace Speckvisit\Parser;

abstract class LeafParser extends Parser
{
    public function parse( $scanner ) 
    {
        $parseResult = $this->getParseResult($scanner);
        if ($parseResult->wasParsingSuccessful())
        {
            if (!$this->discard)
            {
                $scanner->getAssembly()->pushResult($parseResult->getValue());
            }
            
            if (!empty($this->handler)) 
            {
                $this->handler->handleMatch( $scanner->getAssembly() );
            }

	        $scanner->proceedToNextToken();
            $scanner->skipWhiteSpaceTokens();
        }
        return $parseResult;
    }

}

