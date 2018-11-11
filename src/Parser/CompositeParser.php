<?php 
namespace Speckvisit\Parser;


abstract class CompositeParser extends Parser {
    
    public function parse($scanner)
    {
        $parseResult = $this->getParseResult($scanner);
        if ($parseResult->wasParsingSuccessful())
        {
            if (!empty( $this->handler))
            {
                $this->handler->handleMatch( $scanner->getAssembly() );
            }
        }
        return $parseResult;
    }

}
