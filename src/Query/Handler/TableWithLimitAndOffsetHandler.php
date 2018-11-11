<?php
namespace Speckvisit\Query\Handler;

class TableWithLimitAndOffsetHandler
{
    function handleMatch( $assembly ) 
    {
    	$offset = $assembly->popResult();
    	$tableName = $assembly->popResult();
    	$limit = $assembly->popResult();

        $assembly->setTableNameExpression($tableName);
    	$limiter = new \Speckvisit\Specification\Limiter($offset,$limit);
        $assembly->setLimiterExpression( $limiter );
 	}
}

