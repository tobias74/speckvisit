<?php
namespace Speckvisit\Query\Handler;

class SortAscendingHandler 
{
    function handleMatch( $assembly ) 
    {
        $fieldName = $assembly->popResult();
        $assembly->setOrdererExpression( new \Speckvisit\Specification\SingleAscendingOrderer( $fieldName ) );
    }
}
