<?php
namespace Speckvisit\Query\Handler;

class TableNameHandler
{
    function handleMatch( $assembly ) 
    {
        $value = $assembly->popResult();
        $assembly->pushResult( $value );
    }
}
