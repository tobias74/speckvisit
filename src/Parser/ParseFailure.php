<?php 
namespace Speckvisit\Parser;

class ParseFailure extends ParseResult
{
    public function wasParsingSuccessful()
    {
        return false;
    }
}