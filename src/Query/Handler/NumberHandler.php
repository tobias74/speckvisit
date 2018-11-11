<?php
namespace Speckvisit\Query\Handler;

class NumberHandler 
{
    function handleMatch( $assembly ) 
    {
        $value = $assembly->popResult();
        $assembly->pushResult( floatval( $value ) );
    }
}


