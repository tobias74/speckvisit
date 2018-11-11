<?php
namespace Speckvisit\Query\Handler;

class SortDescendingHandler 
{
    function handleMatch( $assembly ) 
    {
        $fieldName = $assembly->popResult();
        $assembly->setOrdererExpression( new \Speckvisit\Specification\SingleDescendingOrderer( $fieldName ) );
    }
}
