<?php
namespace Speckvisit\Query\Handler;

class TableWithLimitHandler
{
    function handleMatch( $assembly ) 
    {
        $tableName = $assembly->popResult();
        $limit = $assembly->popResult();
        $assembly->setTableNameExpression($tableName);
    	$limiter = new \Speckvisit\Specification\Limiter(0, $limit);

        $assembly->setLimiterExpression( $limiter );
    }
}


