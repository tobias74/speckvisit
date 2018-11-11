<?php
namespace Speckvisit\Query\Handler;

class FieldNameHandler 
{
    function handleMatch( $assembly ) 
    {
        $value = $assembly->popResult();
        $assembly->pushResult( $value );
    }
}

