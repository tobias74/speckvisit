<?php
namespace Speckvisit\Query\Handler;

class StringLiteralHandler 
{
    function handleMatch( $assembly ) 
    {
        $value = $assembly->popResult();
        $assembly->pushResult( $value );
    }
}


