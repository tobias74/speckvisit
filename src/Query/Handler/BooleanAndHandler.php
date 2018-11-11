<?php
namespace Speckvisit\Query\Handler;


class BooleanAndHandler
 {
    function handleMatch( $assembly ) 
    {
        $comp1 = $assembly->popResult();
        $comp2 = $assembly->popResult();
        $assembly->pushResult( new \Speckvisit\Specification\AndCriteria($comp1, $comp2) );
    }
}

