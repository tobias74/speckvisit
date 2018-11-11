<?php 
namespace Speckvisit\Parser;


class StringLiteralParser extends LeafParser 
{
    protected function getParseResult($scanner)
    {
        if (!$scanner->getCurrentToken()->isQuotation())
        {
            return new ParseFailure();
        }
        else
        {
            $quotechar = $scanner->getCurrentToken()->getType();
            $returnValue = false;
            $string = "";
            while ( $token = $scanner->proceedToNextToken()->getType() ) 
            {
                if ( $token == $quotechar ) 
                {
                    $returnValue = true;
                    break;
                }
                $string .= $scanner->getCurrentToken()->getValue();
            } 
    
            if ($returnValue)
            {
                return new ParseSuccess($string);
            }
            else
            {
                return new ParseFailure();
            }
        }

    }
    
}
