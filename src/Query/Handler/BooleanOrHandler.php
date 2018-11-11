<?php
namespace Speckvisit\Query\Handler;

class BooleanOrHandler
{
    function handleMatch( $assembly ) 
    {
        $comp1 = $assembly->popResult();
        $comp2 = $assembly->popResult();
        $assembly->pushResult( new \Speckvisit\Specification\OrCriteria($comp1, $comp2) );
    }
}

