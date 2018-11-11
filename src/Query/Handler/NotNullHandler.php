<?php
namespace Speckvisit\Query\Handler;

class NotNullHandler
{
    function handleMatch( $assembly ) 
    {
        $comp1 = $assembly->popResult();
        $assembly->pushResult( new \Speckvisit\Specification\NotNullCriteria( $comp1 ) );
    }
}

