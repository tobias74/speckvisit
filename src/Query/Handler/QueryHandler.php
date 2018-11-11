<?php
namespace Speckvisit\Query\Handler;

class QueryHandler
{
    public function handleMatch( $assembly ) 
    {
    	$query = new \Speckvisit\Query\Query();
        
        $this->tableNameExpression = $assembly->getTableNameExpression();
        $this->criteriaExpression = $assembly->getCriteriaExpression();
        $this->ordererExpression = $assembly->getOrdererExpression();
        $this->limiterExpression = $assembly->getLimiterExpression();

        if ($this->tableNameExpression)
        {
        	$query->setTableName($this->tableNameExpression);
        }

        if ($this->criteriaExpression)
        {
        	$query->setCriteria($this->criteriaExpression);
        }

        if ($this->ordererExpression)
        {
        	$query->setOrderer($this->ordererExpression);
        }
    	
        if ($this->limiterExpression)
        {
        	$query->setLimiter($this->limiterExpression);
        }
        
        
        $assembly->pushResult( $query );
    }
}

