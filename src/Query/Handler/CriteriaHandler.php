<?php
namespace Speckvisit\Query\Handler;

class CriteriaHandler 
{
    function handleMatch( $assembly ) 
    {
        $criteriaExpression = $assembly->popResult();
        $assembly->setCriteriaExpression($criteriaExpression);
    }
}

